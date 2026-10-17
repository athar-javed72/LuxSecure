<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Client\TenancyController as ClientTenancyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', [HomeController::class, '__invoke'])->name('home');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login')->middleware('throttle:login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->name('register')->middleware('throttle:6,1');
Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.request')->middleware('guest');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:6,1');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset')->middleware('guest');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('throttle:6,1');
Route::get('/email/verify', [AuthController::class, 'verifyNotice'])->name('verification.notice')->middleware('auth');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify')->middleware(['auth', 'signed']);
Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])->name('verification.send')->middleware(['auth', 'throttle:6,1']);

Route::get('/properties', [ListingController::class, 'index'])->name('properties');
Route::get('/properties/{listing}', [ListingController::class, 'show'])->name('properties.show');

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::middleware(['auth'])->group(function () {
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
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'panel'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('permission:users.view|users.create|users.update|users.delete')->group(function () {
        Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
        Route::post('users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
        Route::get('users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::middleware('permission:roles.view|roles.create|roles.update|roles.delete|roles.manage_permissions')->group(function () {
        Route::get('roles', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/create', [\App\Http\Controllers\Admin\RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('roles.store');
        Route::get('roles/{role}/edit', [\App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('roles.update');
        Route::delete('roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('roles.destroy');
        Route::get('roles/{role}/permissions', [\App\Http\Controllers\Admin\RoleController::class, 'matrix'])->name('roles.matrix');
        Route::put('roles/{role}/permissions', [\App\Http\Controllers\Admin\RoleController::class, 'updateMatrix'])->name('roles.matrix.update');
    });

    Route::middleware('permission:listings.view.any|listings.view.own|listings.create|listings.update.any|listings.update.own|listings.delete.any|listings.delete.own')
        ->group(function () {
            Route::resource('listings', \App\Http\Controllers\Admin\ListingController::class);
        });

    Route::middleware('permission:listing_types.view|listing_types.create|listing_types.update|listing_types.delete')
        ->group(function () {
            Route::resource('listing-types', \App\Http\Controllers\Admin\ListingTypeController::class)->except(['show']);
        });

    Route::middleware('permission:inquiries.view.any|inquiries.view.own')->group(function () {
        Route::get('inquiries', [\App\Http\Controllers\Admin\InquiryController::class, 'index'])->name('inquiries.index');
        Route::patch('inquiries/{inquiry}', [\App\Http\Controllers\Admin\InquiryController::class, 'markRead'])->name('inquiries.markRead');
    });

    Route::middleware('permission:tenancies.view.any|tenancies.view.own|tenancies.create|tenancies.update.any|tenancies.update.own')->group(function () {
        Route::get('tenancies/overdue', [\App\Http\Controllers\Admin\TenancyController::class, 'overdue'])->name('tenancies.overdue');
        Route::resource('tenancies', \App\Http\Controllers\Admin\TenancyController::class);
        Route::post('rent-payments/{rent_payment}/mark-paid', [\App\Http\Controllers\Admin\RentPaymentController::class, 'markPaid'])
            ->name('rent-payments.mark-paid');
    });

    Route::middleware('permission:listings.view.any|listings.view.own')->group(function () {
        Route::get('room-types', [\App\Http\Controllers\Admin\RoomTypeController::class, 'index'])->name('room-types.index');
        Route::get('room-types/create', [\App\Http\Controllers\Admin\RoomTypeController::class, 'create'])->name('room-types.create');
        Route::post('room-types', [\App\Http\Controllers\Admin\RoomTypeController::class, 'store'])->name('room-types.store');
        Route::get('room-types/{room_type}', [\App\Http\Controllers\Admin\RoomTypeController::class, 'show'])->name('room-types.show');
        Route::delete('room-types/{room_type}', [\App\Http\Controllers\Admin\RoomTypeController::class, 'destroy'])->name('room-types.destroy');
        Route::post('rooms', [\App\Http\Controllers\Admin\RoomTypeController::class, 'storeRoom'])->name('rooms.store');
        Route::post('seasonal-prices', [\App\Http\Controllers\Admin\RoomTypeController::class, 'storeSeasonalPrice'])->name('seasonal-prices.store');
    });

    Route::middleware('permission:bookings.view.any|bookings.view.own|bookings.update.any|bookings.update.own')->group(function () {
        Route::get('bookings', [\App\Http\Controllers\Admin\BookingController::class, 'index'])->name('bookings.index');
        Route::get('bookings/{booking}', [\App\Http\Controllers\Admin\BookingController::class, 'show'])->name('bookings.show');
        Route::patch('bookings/{booking}/status', [\App\Http\Controllers\Admin\BookingController::class, 'updateStatus'])->name('bookings.status');
    });
});
