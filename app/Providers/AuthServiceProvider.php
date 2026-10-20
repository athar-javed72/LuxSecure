<?php

namespace App\Providers;

use App\Models\AgentProfile;
use App\Models\BlogPost;
use App\Models\Booking;
use App\Models\ContactInquiry;
use App\Models\Faq;
use App\Models\KycDocument;
use App\Models\ContentBlock;
use App\Models\Lead;
use App\Models\MediaAsset;
use App\Models\Listing;
use App\Models\ListingType;
use App\Models\Organization;
use App\Models\Page;
use App\Models\Payment;
use App\Models\RentPayment;
use App\Models\Review;
use App\Models\Role;
use App\Models\RoomType;
use App\Models\Setting;
use App\Models\Tenancy;
use App\Models\Testimonial;
use App\Models\Ticket;
use App\Models\User;
use App\Policies\AgentProfilePolicy;
use App\Policies\BookingPolicy;
use App\Policies\CmsPolicy;
use App\Policies\InquiryPolicy;
use App\Policies\KycDocumentPolicy;
use App\Policies\LeadPolicy;
use App\Policies\ListingPolicy;
use App\Policies\ListingTypePolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\RentPaymentPolicy;
use App\Policies\ReviewPolicy;
use App\Policies\RolePolicy;
use App\Policies\RoomTypePolicy;
use App\Policies\SettingPolicy;
use App\Policies\TenancyPolicy;
use App\Policies\TicketPolicy;
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
        Lead::class => LeadPolicy::class,
        Organization::class => OrganizationPolicy::class,
        Ticket::class => TicketPolicy::class,
        AgentProfile::class => AgentProfilePolicy::class,
        Setting::class => SettingPolicy::class,
        BlogPost::class => CmsPolicy::class,
        Faq::class => CmsPolicy::class,
        Testimonial::class => CmsPolicy::class,
        Page::class => CmsPolicy::class,
        MediaAsset::class => CmsPolicy::class,
        ContentBlock::class => CmsPolicy::class,
        Payment::class => PaymentPolicy::class,
        Review::class => ReviewPolicy::class,
        KycDocument::class => KycDocumentPolicy::class,
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
