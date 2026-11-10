<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminHostelPaymentController;
use App\Http\Controllers\Admin\AdminTokenController;
use App\Http\Controllers\Admin\AmenityController;
use App\Http\Controllers\Admin\CampusController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HostelController as AdminHostelController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\RoomCategoryController;
use App\Http\Controllers\Admin\RoomController as AdminRoomController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingRenewalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HostelController;
use App\Http\Controllers\HostelImageController;
use App\Http\Controllers\Manager\ManagerBookingController;
use App\Http\Controllers\Manager\ManagerDashboardController;
use App\Http\Controllers\Manager\ManagerHostelController;
use App\Http\Controllers\Manager\ManagerHostelPaymentController;
use App\Http\Controllers\Manager\ManagerRoomController;
use App\Http\Controllers\MomoCallbackController;
use App\Http\Controllers\MomoPaymentController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/about-us', [PageController::class, 'about'])->name('about');
Route::get('/contact-us', [PageController::class, 'contact'])->name('contact');
Route::post('/contact-us', [PageController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms-of-service', [PageController::class, 'terms'])->name('terms');
Route::get('/residency-policy', [PageController::class, 'residency'])->name('residency');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('/hostels/{hostel:slug}', [HostelController::class, 'show'])->name('hostel.detail');

Route::get('uploads/hostels/{hostel}/{file}', [HostelImageController::class, 'show'])
    ->where('hostel', '[A-Za-z0-9\-_]+')
    ->where('file', '[A-Za-z0-9\-_\.]+')
    ->name('uploads.hostel');

Route::middleware(['auth'])->group(function () {
    Route::get('/tokens/buy', [TokenController::class, 'buy'])->name('token.buy');
    Route::post('/tokens/pay', [TokenController::class, 'pay'])->name('token.pay');
    Route::get('/tokens/success', [TokenController::class, 'success'])->name('token.success');
});

Route::get('/tokens/callback', [TokenController::class, 'callback'])->name('token.callback');

Route::middleware(['auth', 'booking.token'])->group(function () {
    Route::get('/hostels/{hostel:slug}/book', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/hostels/{hostel:slug}/book', [BookingController::class, 'store'])->name('booking.store');
});
Route::get('/booking/{booking:booking_ref}/confirmation', [BookingController::class, 'confirmation'])->name('booking.confirmation');
Route::get('/booking/{booking:booking_ref}/invoice', [BookingController::class, 'invoice'])->name('booking.invoice');
Route::get('/booking/payment/callback', [BookingController::class, 'paymentCallback'])->name('booking.payment.callback');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::middleware('auth')->group(function () {
    Route::get('/my-account/profile', [AccountController::class, 'profile'])->name('profile');
    Route::post('/my-account/profile', [AccountController::class, 'profileUpdate']);
    Route::get('/my-account/my-bookings', [AccountController::class, 'myBookings'])->name('my-bookings');
    Route::post('/bookings/{booking}/cancel', [AccountController::class, 'cancelBooking'])->name('booking.cancel');
    Route::post('/bookings/{booking}/pay', [BookingController::class, 'pay'])->name('booking.pay');
    Route::post('/bookings/{booking}/pay/momo', [MomoPaymentController::class, 'pay'])->name('booking.pay.momo');
    Route::post('/bookings/{booking}/pay/momo/status', [MomoPaymentController::class, 'status'])->name('booking.pay.momo.status');
    Route::post('/bookings/{booking}/renewal/lock', [BookingRenewalController::class, 'lock'])->name('booking.renewal.lock');
    Route::post('/bookings/{booking}/renewal/pay', [BookingRenewalController::class, 'paystack'])->name('booking.renewal.pay');
    Route::post('/bookings/{booking}/renewal/pay/momo', [BookingRenewalController::class, 'momoPay'])->name('booking.renewal.pay.momo');
    Route::post('/bookings/{booking}/renewal/pay/momo/status', [BookingRenewalController::class, 'momoStatus'])->name('booking.renewal.pay.momo.status');
});

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    Route::get('/hostels', [AdminHostelController::class, 'index'])->name('hostels.index');
    Route::get('/hostels/create', [AdminHostelController::class, 'create'])->name('hostels.create');
    Route::post('/hostels', [AdminHostelController::class, 'store'])->name('hostels.store');
    Route::get('/hostels/{hostel}/edit', [AdminHostelController::class, 'edit'])->name('hostels.edit');
    Route::put('/hostels/{hostel}', [AdminHostelController::class, 'update'])->name('hostels.update');
    Route::delete('/hostels/{hostel}', [AdminHostelController::class, 'destroy'])->name('hostels.destroy');
    Route::post('/hostels/{hostel}/toggle', [AdminHostelController::class, 'toggle'])->name('hostels.toggle');

    Route::get('/hostels/{hostel}/payment-details', [AdminHostelPaymentController::class, 'edit'])->name('hostels.payments.edit');
    Route::put('/hostels/{hostel}/payment-details', [AdminHostelPaymentController::class, 'update'])->name('hostels.payments.update');

    Route::get('/hostels/{hostel}/rooms', [AdminRoomController::class, 'index'])->name('hostels.rooms.index');
    Route::get('/hostels/{hostel}/rooms/create', [AdminRoomController::class, 'create'])->name('hostels.rooms.create');
    Route::post('/hostels/{hostel}/rooms', [AdminRoomController::class, 'store'])->name('hostels.rooms.store');
    Route::get('/hostels/{hostel}/rooms/{room}/edit', [AdminRoomController::class, 'edit'])->name('hostels.rooms.edit');
    Route::put('/hostels/{hostel}/rooms/{room}', [AdminRoomController::class, 'update'])->name('hostels.rooms.update');
    Route::delete('/hostels/{hostel}/rooms/{room}', [AdminRoomController::class, 'destroy'])->name('hostels.rooms.destroy');
    Route::post('/hostels/{hostel}/rooms/{room}/toggle', [AdminRoomController::class, 'toggle'])->name('hostels.rooms.toggle');

    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');

    Route::get('/tokens', [AdminTokenController::class, 'index'])->name('tokens.index');

    Route::get('/campuses', [CampusController::class, 'index'])->name('campuses.index');
    Route::get('/campuses/create', [CampusController::class, 'create'])->name('campuses.create');
    Route::post('/campuses', [CampusController::class, 'store'])->name('campuses.store');
    Route::get('/campuses/{campus}/edit', [CampusController::class, 'edit'])->name('campuses.edit');
    Route::put('/campuses/{campus}', [CampusController::class, 'update'])->name('campuses.update');
    Route::delete('/campuses/{campus}', [CampusController::class, 'destroy'])->name('campuses.destroy');
    Route::post('/campuses/{campus}/toggle', [CampusController::class, 'toggle'])->name('campuses.toggle');

    Route::get('/amenities', [AmenityController::class, 'index'])->name('amenities.index');
    Route::get('/amenities/create', [AmenityController::class, 'create'])->name('amenities.create');
    Route::post('/amenities', [AmenityController::class, 'store'])->name('amenities.store');
    Route::get('/amenities/{amenity}/edit', [AmenityController::class, 'edit'])->name('amenities.edit');
    Route::put('/amenities/{amenity}', [AmenityController::class, 'update'])->name('amenities.update');
    Route::delete('/amenities/{amenity}', [AmenityController::class, 'destroy'])->name('amenities.destroy');
    Route::post('/amenities/{amenity}/toggle', [AmenityController::class, 'toggle'])->name('amenities.toggle');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::delete('/messages/{id}', [MessageController::class, 'destroy'])->name('messages.destroy');

    Route::get('/room-categories', [RoomCategoryController::class, 'index'])->name('room-categories.index');
    Route::get('/room-categories/create', [RoomCategoryController::class, 'create'])->name('room-categories.create');
    Route::post('/room-categories', [RoomCategoryController::class, 'store'])->name('room-categories.store');
    Route::get('/room-categories/{roomCategory}/edit', [RoomCategoryController::class, 'edit'])->name('room-categories.edit');
    Route::put('/room-categories/{roomCategory}', [RoomCategoryController::class, 'update'])->name('room-categories.update');
    Route::delete('/room-categories/{roomCategory}', [RoomCategoryController::class, 'destroy'])->name('room-categories.destroy');
    Route::post('/room-categories/{roomCategory}/toggle', [RoomCategoryController::class, 'toggle'])->name('room-categories.toggle');
});

Route::prefix('manager')->middleware(['auth', 'manager'])->name('manager.')->group(function () {
    Route::get('/', [ManagerDashboardController::class, 'index'])->name('dashboard');

    Route::get('/hostels', [ManagerHostelController::class, 'index'])->name('hostels.index');
    Route::get('/hostels/{hostel}/edit', [ManagerHostelController::class, 'edit'])->name('hostels.edit');
    Route::put('/hostels/{hostel}', [ManagerHostelController::class, 'update'])->name('hostels.update');

    Route::get('/hostels/{hostel}/payment-details', [ManagerHostelPaymentController::class, 'edit'])->name('hostels.payments.edit');
    Route::put('/hostels/{hostel}/payment-details', [ManagerHostelPaymentController::class, 'update'])->name('hostels.payments.update');

    Route::get('/hostels/{hostel}/rooms', [ManagerRoomController::class, 'index'])->name('hostels.rooms.index');
    Route::get('/hostels/{hostel}/rooms/create', [ManagerRoomController::class, 'create'])->name('hostels.rooms.create');
    Route::post('/hostels/{hostel}/rooms', [ManagerRoomController::class, 'store'])->name('hostels.rooms.store');
    Route::get('/hostels/{hostel}/rooms/{room}/edit', [ManagerRoomController::class, 'edit'])->name('hostels.rooms.edit');
    Route::put('/hostels/{hostel}/rooms/{room}', [ManagerRoomController::class, 'update'])->name('hostels.rooms.update');
    Route::delete('/hostels/{hostel}/rooms/{room}', [ManagerRoomController::class, 'destroy'])->name('hostels.rooms.destroy');
    Route::post('/hostels/{hostel}/rooms/{room}/toggle', [ManagerRoomController::class, 'toggle'])->name('hostels.rooms.toggle');

    Route::get('/bookings', [ManagerBookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings/{booking}/status', [ManagerBookingController::class, 'updateStatus'])->name('bookings.status');
});