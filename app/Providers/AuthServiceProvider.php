<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\ContactInquiry;
use App\Models\Listing;
use App\Models\ListingType;
use App\Models\RentPayment;
use App\Models\Role;
use App\Models\RoomType;
use App\Models\Tenancy;
use App\Models\User;
use App\Policies\BookingPolicy;
use App\Policies\InquiryPolicy;
use App\Policies\ListingPolicy;
use App\Policies\ListingTypePolicy;
use App\Policies\RentPaymentPolicy;
use App\Policies\RolePolicy;
use App\Policies\RoomTypePolicy;
use App\Policies\TenancyPolicy;
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
        Tenancy::class => TenancyPolicy::class,
        RentPayment::class => RentPaymentPolicy::class,
        Booking::class => BookingPolicy::class,
        RoomType::class => RoomTypePolicy::class,
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
