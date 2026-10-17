<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserAccessRevoker
{
    public function revoke(User $user): void
    {
        $user->tokens()->delete();

        Cache::forever($this->cacheKey($user->id), now()->getTimestamp());

        if (Schema::hasTable('sessions') && Schema::hasColumn('sessions', 'user_id')) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }
    }

    public function markSessionEstablished(Request $request): void
    {
        if ($request->hasSession()) {
            $request->session()->put('auth.established_at', now()->getTimestamp());
        }
    }

    public function isSessionRevoked(User $user, Request $request): bool
    {
        $revokedAt = Cache::get($this->cacheKey($user->id));
        if (! $revokedAt) {
            return false;
        }

        // API tokens were deleted on revoke; bearer auth that still resolves is stale only if token survived.
        if ($request->is('api/*') || ! $request->hasSession()) {
            return false;
        }

        $established = (int) $request->session()->get('auth.established_at', 0);

        return $established < (int) $revokedAt;
    }

    private function cacheKey(int $userId): string
    {
        return 'user.access_revoked_at.'.$userId;
    }
}
