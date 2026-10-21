<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\TwoFactorService;
use App\Services\UserAccessRevoker;
use App\Services\UserPrivilegedAttributes;
use App\Support\CountryContext;
use App\Support\LegalAcceptanceService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        $redirect = $request->query('redirect');
        if (is_string($redirect) && $redirect !== '') {
            if (str_starts_with($redirect, '/') && ! str_starts_with($redirect, '//')) {
                $request->session()->put('url.intended', url($redirect));
            }
        }

        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function login(LoginRequest $request, TwoFactorService $twoFactor, UserAccessRevoker $accessRevoker)
    {
        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $user = Auth::user();
            if (! $user->is_active) {
                Auth::logout();

                return back()->withErrors(['email' => 'This account has been deactivated.']);
            }

            if ($twoFactor->requiresTwoFactor($user)) {
                Auth::logout();
                $request->session()->put('login.two_factor_user_id', $user->id);
                $request->session()->put('login.two_factor_remember', $request->boolean('remember'));

                return redirect()->route('two-factor.challenge');
            }

            $request->session()->regenerate();
            $accessRevoker->markSessionEstablished($request);

            if ($twoFactor->requiresSetup($user)) {
                return redirect()->route('admin.two-factor.show')
                    ->with('error', 'Please set up two-factor authentication to continue.');
            }

            if ($user->can('panel.access')) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('profile'));
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function showTwoFactorChallenge()
    {
        if (! session()->has('login.two_factor_user_id')) {
            return redirect()->route('login.form');
        }

        return view('auth.two-factor-challenge');
    }

    public function verifyTwoFactorChallenge(Request $request, TwoFactorService $twoFactor)
    {
        $request->validate(['code' => ['required', 'string']]);
        $userId = $request->session()->get('login.two_factor_user_id');
        $user = $userId ? User::find($userId) : null;

        if (! $user || ! $twoFactor->verifyUser($user, $request->string('code')->toString())) {
            return back()->withErrors(['code' => 'Invalid authentication code.']);
        }

        Auth::login($user, (bool) $request->session()->pull('login.two_factor_remember'));
        $request->session()->forget('login.two_factor_user_id');
        $request->session()->regenerate();
        app(UserAccessRevoker::class)->markSessionEstablished($request);

        if ($user->can('panel.access')) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('profile'));
    }

    public function register(RegisterRequest $request)
    {
        $privileged = app(UserPrivilegedAttributes::class);
        $country = CountryContext::current();
        $user = $privileged->createActive([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone ?: null,
            'password' => Hash::make($request->password),
            'email_verified_at' => app()->environment('local') ? now() : null,
            'country_id' => $country?->id,
            'preferred_locale' => session('locale', $country?->default_locale),
            'preferred_currency' => session('currency', $country?->currency_code),
        ], true);

        $user->assignRole('client');

        LegalAcceptanceService::recordFromRequest(
            $user,
            [
                'accept_terms' => $request->boolean('accept_terms'),
                'accept_privacy' => $request->boolean('accept_privacy'),
            ],
            $request->ip()
        );

        event(new Registered($user));

        $message = app()->environment('local')
            ? 'Registration successful. You can login now.'
            : 'Registration successful. Please verify your email and login.';

        return redirect('login')->with('success', $message);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('status', 'Logged out successfully.');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save();
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect('login')->with('success', 'Your password has been reset.');
        }

        return back()->withErrors(['email' => __($status)]);
    }

    public function verifyNotice()
    {
        return view('auth.verify-email');
    }

    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403);
        }
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('profile')->with('success', 'Email already verified.');
        }
        $user->markEmailAsVerified();
        event(new Verified($user));

        return redirect()->route('profile')->with('success', 'Email verified successfully.');
    }

    public function resendVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('profile');
        }
        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Verification link sent!');
    }
}
