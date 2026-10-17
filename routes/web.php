<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AgentProfileController;
use App\Http\Controllers\Admin\CancellationPolicyController;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\CountryPaymentMethodController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\KycDocumentTypeController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\ListingTypeController;
use App\Http\Controllers\Admin\OrganizationReportController;
use App\Http\Controllers\Admin\RentPaymentController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TenancyController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\TwoFactorController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Client\TenancyController as ClientTenancyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KycDocumentController;
use App\Http\Controllers\LegalAcceptanceController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\PhoneVerificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', [HomeController::class, '__invoke'])->name('home');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login')->middleware('throttle:login');
Route::get('/two-factor-challenge', [AuthController::class, 'showTwoFactorChallenge'])->name('two-factor.challenge')->middleware('guest');
Route::post('/two-factor-challenge', [AuthController::class, 'verifyTwoFactorChallenge'])->name('two-factor.verify')->middleware(['guest', 'throttle:two-factor']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->name('register')->middleware('throttle:6,1');

Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.request')->middleware('guest');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:6,1');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset')->middleware('guest');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('throttle:6,1');
Route::get('/email/verify', [AuthController::class, 'verifyNotice'])->name('verification.notice')->middleware(['auth', 'active']);
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify')->middleware(['auth', 'active', 'signed']);
Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])->name('verification.send')->middleware(['auth', 'active', 'throttle:6,1']);

Route::get('/properties', [ListingController::class, 'index'])->name('properties');
Route::get('/properties/{listing}', [ListingController::class, 'show'])->name('properties.show');

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:contact');

Route::get('/blog', [PageController::class, 'blogIndex'])->name('blog.index');
Route::get('/blog/{slug}', [PageController::class, 'blogShow'])->name('blog.show');
Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');
Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');
Route::get('/organizations/{organization}', [OrganizationController::class, 'show'])->name('organizations.show');

Route::post('/webhooks/payments/jazzcash', [PaymentWebhookController::class, 'jazzcash'])
    ->name('webhooks.payments.jazzcash');
Route::post('/webhooks/payments/easypaisa', [PaymentWebhookController::class, 'easypaisa'])
    ->name('webhooks.payments.easypaisa');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/legal/accept', [LegalAcceptanceController::class, 'show'])->name('legal.accept');
    Route::post('/legal/accept', [LegalAcceptanceController::class, 'store'])->name('legal.accept.store');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/favorites/{listing}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    Route::get('/bookings/quote', [BookingController::class, 'quote'])->name('bookings.quote');
    Route::get('/bookings/mine', [BookingController::class, 'mine'])->name('bookings.mine');
    Route::get('/properties/{listing}/book', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    Route::get('/tenancies/mine', [ClientTenancyController::class, 'index'])->name('tenancies.mine');
    Route::get('/tenancies/{tenancy}', [ClientTenancyController::class, 'show'])->name('tenancies.show');

    Route::post('/payments/initiate', [PaymentController::class, 'initiate'])->name('payments.initiate');
    Route::post('/properties/{listing}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    Route::get('/kyc', [KycDocumentController::class, 'index'])->name('kyc.index');
    Route::post('/kyc', [KycDocumentController::class, 'store'])->name('kyc.store');

    Route::get('/phone/verify', [PhoneVerificationController::class, 'show'])->name('phone.verify');
    Route::post('/phone/verify/send', [PhoneVerificationController::class, 'send'])
        ->middleware('throttle:3,10')
        ->name('phone.verify.send');
    Route::post('/phone/verify', [PhoneVerificationController::class, 'verify'])
        ->middleware('throttle:10,10')
        ->name('phone.verify.confirm');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'active', 'panel', 'panel.2fa'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('permission:users.view|users.create|users.update|users.delete')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/export', [UserController::class, 'export'])->name('users.export');
        Route::post('users/bulk', [UserController::class, 'bulk'])->name('users.bulk');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::middleware('permission:roles.view|roles.create|roles.update|roles.delete|roles.manage_permissions')->group(function () {
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::get('roles/{role}/permissions', [RoleController::class, 'matrix'])->name('roles.matrix');
        Route::put('roles/{role}/permissions', [RoleController::class, 'updateMatrix'])->name('roles.matrix.update');
    });

    Route::middleware('permission:listings.view.any|listings.view.own|listings.create|listings.update.any|listings.update.own|listings.delete.any|listings.delete.own')
        ->group(function () {
            Route::get('listings/export', [App\Http\Controllers\Admin\ListingController::class, 'export'])->name('listings.export');
            Route::post('listings/bulk', [App\Http\Controllers\Admin\ListingController::class, 'bulk'])->name('listings.bulk');
            Route::delete('listings/{listing}/images/{image}', [App\Http\Controllers\Admin\ListingController::class, 'destroyImage'])->name('listings.images.destroy');
            Route::post('listings/{listing}/images/reorder', [App\Http\Controllers\Admin\ListingController::class, 'reorderImages'])->name('listings.images.reorder');
            Route::get('listings/{listing}/private-document', [App\Http\Controllers\Admin\ListingController::class, 'downloadPrivateDocument'])->name('listings.private-document');
            Route::resource('listings', App\Http\Controllers\Admin\ListingController::class);
        });

    Route::middleware('permission:listing_types.view|listing_types.create|listing_types.update|listing_types.delete')
        ->group(function () {
            Route::resource('listing-types', ListingTypeController::class)->except(['show']);
        });

    Route::middleware('permission:inquiries.view.any|inquiries.view.own')->group(function () {
        Route::get('inquiries', [InquiryController::class, 'index'])->name('inquiries.index');
        Route::patch('inquiries/{inquiry}', [InquiryController::class, 'markRead'])->name('inquiries.markRead');
    });

    Route::middleware('permission:leads.view.any|leads.view.own|leads.create|leads.update.any|leads.update.own')->group(function () {
        Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
        Route::get('leads/export', [LeadController::class, 'export'])->name('leads.export');
        Route::post('leads/bulk', [LeadController::class, 'bulk'])->name('leads.bulk');
        Route::post('leads/convert/{inquiry}', [LeadController::class, 'convert'])->name('leads.convert');
        Route::get('leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
        Route::put('leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
        Route::delete('leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
    });

    Route::middleware('permission:tickets.view.any|tickets.view.own|tickets.create|tickets.update.any|tickets.update.own')->group(function () {
        Route::get('tickets', [TicketController::class, 'index'])->name('tickets.index');
        Route::get('tickets/create', [TicketController::class, 'create'])->name('tickets.create');
        Route::post('tickets', [TicketController::class, 'store'])->name('tickets.store');
        Route::get('tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
        Route::put('tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
        Route::post('tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
    });

    Route::middleware('permission:agents.view|agents.update|agents.manage')->group(function () {
        Route::resource('agents', AgentProfileController::class)
            ->parameters(['agents' => 'agent_profile'])
            ->except(['show']);
    });

    Route::middleware('permission:cms.view|cms.create|cms.update|cms.delete')->group(function () {
        Route::get('cms', [CmsController::class, 'dashboard'])->name('cms.index');
        Route::get('cms/posts', [CmsController::class, 'posts'])->name('cms.posts.index');
        Route::get('cms/posts/create', [CmsController::class, 'createPost'])->name('cms.posts.create');
        Route::post('cms/posts', [CmsController::class, 'storePost'])->name('cms.posts.store');
        Route::get('cms/posts/{post}/edit', [CmsController::class, 'editPost'])->name('cms.posts.edit');
        Route::put('cms/posts/{post}', [CmsController::class, 'updatePost'])->name('cms.posts.update');
        Route::delete('cms/posts/{post}', [CmsController::class, 'destroyPost'])->name('cms.posts.destroy');
        Route::get('cms/faqs', [CmsController::class, 'faqs'])->name('cms.faqs.index');
        Route::post('cms/faqs', [CmsController::class, 'storeFaq'])->name('cms.faqs.store');
        Route::put('cms/faqs/{faq}', [CmsController::class, 'updateFaq'])->name('cms.faqs.update');
        Route::delete('cms/faqs/{faq}', [CmsController::class, 'destroyFaq'])->name('cms.faqs.destroy');
        Route::get('cms/testimonials', [CmsController::class, 'testimonials'])->name('cms.testimonials.index');
        Route::post('cms/testimonials', [CmsController::class, 'storeTestimonial'])->name('cms.testimonials.store');
        Route::put('cms/testimonials/{testimonial}', [CmsController::class, 'updateTestimonial'])->name('cms.testimonials.update');
        Route::delete('cms/testimonials/{testimonial}', [CmsController::class, 'destroyTestimonial'])->name('cms.testimonials.destroy');
        Route::get('cms/pages', [CmsController::class, 'pages'])->name('cms.pages.index');
        Route::post('cms/pages', [CmsController::class, 'storePage'])->name('cms.pages.store');
        Route::put('cms/pages/{page}', [CmsController::class, 'updatePage'])->name('cms.pages.update');
        Route::delete('cms/pages/{page}', [CmsController::class, 'destroyPage'])->name('cms.pages.destroy');
    });

    Route::middleware('permission:settings.view|settings.update')->group(function () {
        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });

    Route::middleware('permission:activity_log.view')->group(function () {
        Route::get('activity', [ActivityLogController::class, 'index'])->name('activity.index');
    });

    Route::middleware('permission:tenancies.view.any|tenancies.view.own|tenancies.create|tenancies.update.any|tenancies.update.own')->group(function () {
        Route::get('tenancies/overdue', [TenancyController::class, 'overdue'])->name('tenancies.overdue');
        Route::resource('tenancies', TenancyController::class);
        Route::post('rent-payments/{rent_payment}/mark-paid', [RentPaymentController::class, 'markPaid'])
            ->name('rent-payments.mark-paid');
    });

    Route::middleware('permission:rooms.view.any|rooms.view.own|rooms.create|rooms.update.any|rooms.update.own')->group(function () {
        Route::get('room-types', [RoomTypeController::class, 'index'])->name('room-types.index');
        Route::get('room-types/create', [RoomTypeController::class, 'create'])->name('room-types.create');
        Route::post('room-types', [RoomTypeController::class, 'store'])->name('room-types.store');
        Route::get('room-types/{room_type}', [RoomTypeController::class, 'show'])->name('room-types.show');
        Route::delete('room-types/{room_type}', [RoomTypeController::class, 'destroy'])->name('room-types.destroy');
        Route::post('rooms', [RoomTypeController::class, 'storeRoom'])->name('rooms.store');
        Route::post('seasonal-prices', [RoomTypeController::class, 'storeSeasonalPrice'])->name('seasonal-prices.store');
    });

    Route::middleware('permission:bookings.view.any|bookings.view.own|bookings.update.any|bookings.update.own')->group(function () {
        Route::get('bookings', [App\Http\Controllers\Admin\BookingController::class, 'index'])->name('bookings.index');
        Route::get('bookings/{booking}', [App\Http\Controllers\Admin\BookingController::class, 'show'])->name('bookings.show');
        Route::patch('bookings/{booking}/status', [App\Http\Controllers\Admin\BookingController::class, 'updateStatus'])->name('bookings.status');
    });

    Route::middleware('permission:payments.view.any|payments.view.own')->group(function () {
        Route::get('payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/export', [App\Http\Controllers\Admin\PaymentController::class, 'export'])->name('payments.export');
        Route::get('payments/{payment}', [App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payments.show');
        Route::get('payments/{payment}/invoice', [App\Http\Controllers\Admin\PaymentController::class, 'downloadInvoice'])->name('payments.invoice');
    });

    Route::post('payments/{payment}/approve', [App\Http\Controllers\Admin\PaymentController::class, 'approve'])
        ->middleware('permission:payments.update.any')
        ->name('payments.approve');
    Route::post('payments/{payment}/refund', [App\Http\Controllers\Admin\PaymentController::class, 'refund'])
        ->middleware('permission:payments.refund')
        ->name('payments.refund');

    Route::middleware('permission:reviews.view.any|reviews.moderate')->group(function () {
        Route::get('reviews', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
        Route::patch('reviews/{review}', [App\Http\Controllers\Admin\ReviewController::class, 'moderate'])->name('reviews.moderate');
    });

    Route::middleware('permission:kyc.view.any|kyc.review')->group(function () {
        Route::get('kyc', [App\Http\Controllers\Admin\KycDocumentController::class, 'index'])->name('kyc.index');
        Route::patch('kyc/{kyc}', [App\Http\Controllers\Admin\KycDocumentController::class, 'review'])->name('kyc.review');
        Route::get('kyc/{kyc}/download', [App\Http\Controllers\Admin\KycDocumentController::class, 'download'])->name('kyc.download');
    });

    Route::middleware('permission:countries.view|countries.create|countries.update|countries.delete')->group(function () {
        Route::get('countries/{country}/settings', [CountryController::class, 'settings'])->name('countries.settings');
        Route::put('countries/{country}/settings', [CountryController::class, 'updateSettings'])->name('countries.settings.update');
        Route::resource('countries', CountryController::class)->except(['show', 'destroy']);

        Route::resource('kyc-document-types', KycDocumentTypeController::class)->except(['show']);
        Route::get('country-payment-methods', [CountryPaymentMethodController::class, 'index'])->name('country-payment-methods.index');
        Route::put('country-payment-methods', [CountryPaymentMethodController::class, 'update'])->name('country-payment-methods.update');
    });

    Route::middleware('permission:organizations.view|organizations.create|organizations.update|organizations.delete')->group(function () {
        Route::get('organizations/{organization}/report', [OrganizationReportController::class, 'show'])->name('organizations.report');
        Route::resource('organizations', App\Http\Controllers\Admin\OrganizationController::class)->except(['show']);
    });

    Route::middleware('permission:cancellation_policies.view|cancellation_policies.create|cancellation_policies.update|cancellation_policies.delete')->group(function () {
        Route::get('cancellation-policies/{cancellation_policy}/preview', [CancellationPolicyController::class, 'preview'])
            ->name('cancellation-policies.preview');
        Route::resource('cancellation-policies', CancellationPolicyController::class)->except(['show']);
    });

    Route::middleware('permission:currencies.view|currencies.create|currencies.update|currencies.delete')->group(function () {
        Route::resource('currencies', CurrencyController::class)->except(['show']);
    });

    Route::middleware('permission:languages.view|languages.create|languages.update|languages.delete')->group(function () {
        Route::resource('languages', LanguageController::class)->except(['show']);
    });

    Route::get('two-factor', [TwoFactorController::class, 'show'])->name('two-factor.show');
    Route::get('two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::post('two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('two-factor.confirm');
    Route::post('two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
});
