<?php

namespace App\Providers;

use App\Models\ContactInquiry;
use App\Models\Listing;
use App\Models\ListingType;
use App\Models\Role;
use App\Models\User;
use App\Policies\InquiryPolicy;
use App\Policies\ListingPolicy;
use App\Policies\ListingTypePolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Listing::class => ListingPolicy::class,
        ListingType::class => ListingTypePolicy::class,
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        ContactInquiry::class => InquiryPolicy::class,
    ];

    public function boot(): void
    {
        Gate::before(function (User $user, string $ability) {
            if ($user->is_active && $user->hasRole('super_admin')) {
                return true;
            }

            return null;
        });
    }
}
