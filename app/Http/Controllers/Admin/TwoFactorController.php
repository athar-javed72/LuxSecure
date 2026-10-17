<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function __construct(private readonly TwoFactorService $twoFactor) {}

    public function show(Request $request): View
    {
        abort_unless($request->user()->can('panel.access'), 403);

        return view('admin.two-factor.show', [
            'enabled' => $request->user()->hasTwoFactorEnabled(),
        ]);
    }

    public function enable(Request $request): RedirectResponse|View
    {
        abort_unless($request->user()->can('panel.access'), 403);
        $secret = $this->twoFactor->generateSecret();
        $request->session()->put('admin.2fa.setup_secret', $secret);

        return view('admin.two-factor.confirm', [
            'secret' => $secret,
            'otpauthUrl' => $this->twoFactor->qrUrl($request->user(), $secret),
        ]);
    }

    public function confirm(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('panel.access'), 403);
        $request->validate(['code' => ['required', 'string']]);
        $secret = $request->session()->get('admin.2fa.setup_secret');

        if (! $secret || ! $this->twoFactor->confirm($request->user(), $secret, $request->string('code')->toString())) {
            return back()->withErrors(['code' => 'Invalid authentication code.']);
        }

        $request->session()->forget('admin.2fa.setup_secret');

        return redirect()->route('admin.two-factor.show')->with('success', 'Two-factor authentication enabled.');
    }

    public function disable(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('panel.access'), 403);
        $request->validate(['code' => ['required', 'string']]);

        if (! $this->twoFactor->verifyUser($request->user(), $request->string('code')->toString())) {
            return back()->withErrors(['code' => 'Invalid authentication code.']);
        }

        $this->twoFactor->disable($request->user());

        return redirect()->route('admin.two-factor.show')->with('success', 'Two-factor authentication disabled.');
    }
}
