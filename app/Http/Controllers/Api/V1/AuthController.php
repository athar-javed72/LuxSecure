<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\TwoFactorService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

/**
 * @group Auth
 */
class AuthController extends Controller
{
    public function __construct(private readonly TwoFactorService $twoFactor) {}

    /**
     * Register a client account.
     *
     * @unauthenticated
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'is_active' => true,
            'email_verified_at' => app()->environment('local') ? now() : null,
        ]);
        $user->assignRole('client');

        $token = $user->createToken('api')->plainTextToken;

        return ApiResponse::success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user->load('roles')),
        ], 'Registered', 201);
    }

    /**
     * Login and receive a Sanctum token.
     *
     * @unauthenticated
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:80'],
            'two_factor_code' => ['nullable', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        if (! $user->is_active) {
            return ApiResponse::error('Account deactivated.', 403, null, 'account_inactive');
        }

        if ($this->twoFactor->requiresTwoFactor($user)) {
            if (blank($credentials['two_factor_code'] ?? null)) {
                return ApiResponse::success([
                    'two_factor_required' => true,
                    'two_factor_token' => $this->twoFactor->issueChallengeToken($user),
                ], 'Two-factor authentication required.');
            }

            if (! $this->twoFactor->verifyUser($user, $credentials['two_factor_code'])) {
                return ApiResponse::error('Invalid two-factor code.', 422, ['two_factor_code' => ['Invalid code.']], 'invalid_2fa');
            }
        }

        $token = $user->createToken($credentials['device_name'] ?? 'api')->plainTextToken;

        return ApiResponse::success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user->load('roles')),
        ], 'Authenticated');
    }

    /**
     * Complete a two-factor challenge from login.
     *
     * @unauthenticated
     */
    public function twoFactorChallenge(Request $request): JsonResponse
    {
        $data = $request->validate([
            'two_factor_token' => ['required', 'string'],
            'code' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:80'],
        ]);

        $user = $this->twoFactor->userFromChallengeToken($data['two_factor_token']);
        if (! $user || ! $this->twoFactor->verifyUser($user, $data['code'])) {
            return ApiResponse::error('Invalid or expired two-factor challenge.', 422, null, 'invalid_2fa');
        }

        $token = $user->createToken($data['device_name'] ?? 'api')->plainTextToken;

        return ApiResponse::success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user->load('roles')),
        ], 'Authenticated');
    }

    /**
     * Logout current token.
     *
     * @authenticated
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return ApiResponse::success(null, 'Logged out');
    }
}
