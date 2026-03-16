<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\VehicleDocumentController;
use App\Http\Controllers\Admin\BreakdownController;
use App\Http\Controllers\Admin\FuelRequestController as AdminFuelRequestController;
use App\Http\Controllers\Admin\FuelLogController;
use App\Http\Controllers\Admin\TransportRequestController;
use App\Http\Controllers\Admin\LocationLogController;
use App\Http\Controllers\Driver\TrackingController;
use App\Http\Controllers\Admin\PaymentRequestController;
use App\Http\Controllers\Driver\FuelRequestController as DriverFuelRequestController;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Laravel\Jetstream\Jetstream;


Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [\Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::class, 'store']);

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', [\Laravel\Fortify\Http\Controllers\RegisteredUserController::class, 'store']);

if (Features::enabled(Features::resetPasswords())) {
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::post('/forgot-password', [\Laravel\Fortify\Http\Controllers\PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('/reset-password/{token}', function (string $token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');

    Route::post('/reset-password', [\Laravel\Fortify\Http\Controllers\NewPasswordController::class, 'store'])
        ->name('password.update');
}

// ────────────────────────────────────────────────
// Dashboards
// ────────────────────────────────────────────────

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard')->middleware(['auth', 'role:admin_level_1,admin_level_2']);

Route::get('/driver/dashboard', function () {
    return view('driver.dashboard');
})->name('driver.dashboard')->middleware(['auth', 'role:driver']);

Route::get('/worker/dashboard', function () {
    return view('worker.dashboard');
})->name('worker.dashboard')->middleware(['auth', 'role:worker']);

// Default Jetstream dashboard
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// ────────────────────────────────────────────────
// Admin Routes
// ────────────────────────────────────────────────

Route::prefix('admin')->middleware(['auth', 'role:admin_level_1,admin_level_2'])->group(function () {

    // Dashboard (duplicate removed - already defined above)
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Users & Roles
    Route::get('/users/manage-roles', [UserController::class, 'manageRoles'])->name('admin.users.manage-roles');
    Route::post('/users/{user}/update-role', [UserController::class, 'updateRole'])->name('admin.users.update-role');
    Route::resource('users', UserController::class)->names('admin.users');

    // Drivers, Workers, Vehicles
    Route::resource('drivers', DriverController::class)->names('admin.drivers');
    Route::resource('workers', WorkerController::class)->names('admin.workers');
    Route::resource('vehicles', VehicleController::class)->names('admin.vehicles');

    // Vehicle Documents
    Route::get('/vehicledocuments', [VehicleDocumentController::class, 'index'])->name('admin.vehicledocuments.index');
    Route::resource('vehicledocuments', VehicleDocumentController::class)->names('admin.vehicledocuments')->except(['index']);
    Route::get('/vehicledocuments/vehicle/{vehicle}', [VehicleDocumentController::class, 'vehicleDocuments'])->name('admin.vehicledocuments.vehicle');

    // Assign Driver
    Route::post('vehicles/{vehicle}/assign-driver', [VehicleController::class, 'assignDriver'])->name('admin.vehicles.assign-driver');

    // Breakdowns
    Route::resource('breakdowns', BreakdownController::class)->names('admin.breakdowns');
    Route::post('breakdowns/{breakdown}/approve', [BreakdownController::class, 'approve'])->name('admin.breakdowns.approve');

    // Fuel Requests (Admin side)
    Route::resource('fuel-requests', AdminFuelRequestController::class)->names('admin.fuel-requests');
    Route::post('fuel-requests/{fuel_request}/approve', [AdminFuelRequestController::class, 'approve'])->name('admin.fuel-requests.approve');
    Route::post('fuel-requests/{fuel_request}/reject', [AdminFuelRequestController::class, 'reject'])->name('admin.fuel-requests.reject');

    // Fuel Logs
    Route::resource('fuel-logs', FuelLogController::class)->names('admin.fuel-logs');

    // Transport Requests
    Route::resource('transport-requests', TransportRequestController::class)->names('admin.transport-requests');
    Route::post('transport-requests/{transport_request}/approve', [TransportRequestController::class, 'approve'])->name('admin.transport-requests.approve');
    Route::post('transport-requests/{transport_request}/reject', [TransportRequestController::class, 'reject'])->name('admin.transport-requests.reject');
    Route::post('transport-requests/{transport_request}/assign', [TransportRequestController::class, 'assign'])->name('admin.transport-requests.assign');

    // Location Logs & Tracking
    Route::get('/location-logs', [LocationLogController::class, 'index'])->name('admin.location-logs.index');
    Route::post('/vehicles/{vehicle}/generate-tracking-link', [LocationLogController::class, 'generateLink'])->name('admin.vehicles.generate-tracking-link');
    Route::get('/vehicles/{vehicle}/latest-location', [LocationLogController::class, 'latestLocation'])->name('admin.vehicles.latest-location');



    // Payment Requests (Admin)
    Route::resource('payment-requests', PaymentRequestController::class)->only(['index', 'show'])->names('admin.payment-requests');
    Route::post('payment-requests/{paymentRequest}/approve', [PaymentRequestController::class, 'approve'])->name('admin.payment-requests.approve');
    Route::post('payment-requests/{paymentRequest}/reject', [PaymentRequestController::class, 'reject'])->name('admin.payment-requests.reject');
    // Fuel Requests - Payment actions
    Route::post('fuel-requests/{fuel_request}/approvePayment', [AdminFuelRequestController::class, 'approvePayment'])
    ->name('admin.fuel-requests.approvePayment');

    Route::post('fuel-requests/{fuel_request}/rejectPayment', [AdminFuelRequestController::class, 'rejectPayment'])
    ->name('admin.fuel-requests.rejectPayment');
 
});


    

// ────────────────────────────────────────────────
// Public Tracking Route
// ────────────────────────────────────────────────

Route::get('/tracking/{token}', [TrackingController::class, 'index'])->name('tracking.index');
Route::post('/tracking/{token}/location', [TrackingController::class, 'storeLocation'])->name('tracking.location');

// ────────────────────────────────────────────────
// Driver Routes
// ────────────────────────────────────────────────

Route::prefix('driver')->middleware(['auth', 'role:driver'])->group(function () {

    // Dashboard (already defined above - kept for clarity)
    Route::get('/dashboard', function () {
        return view('driver.dashboard');
    })->name('driver.dashboard');

    // Fuel Requests (Driver side - using DriverFuelRequestController)
    Route::get('fuel-requests', [DriverFuelRequestController::class, 'index'])->name('driver.fuel-requests.index');
    Route::get('fuel-requests/create', [DriverFuelRequestController::class, 'create'])->name('driver.fuel-requests.create');
    Route::post('fuel-requests', [DriverFuelRequestController::class, 'store'])->name('driver.fuel-requests.store');
    Route::get('fuel-requests/{fuelRequest}', [DriverFuelRequestController::class, 'show'])->name('driver.fuel-requests.show');
    Route::get('fuel-requests/{fuelRequest}/complete', [DriverFuelRequestController::class, 'complete'])->name('driver.fuel-requests.complete');
    Route::post('fuel-requests/{fuelRequest}/complete', [DriverFuelRequestController::class, 'storeCompletion'])->name('driver.fuel-requests.store-completion');
    Route::post('fuel-requests/{fuelRequest}/request-payment', [DriverFuelRequestController::class, 'requestPayment'])->name('driver.fuel-requests.request-payment');
    Route::get('/tracking/{token}', [TrackingController::class, 'index'])->name('driver.tracking.index');
    Route::post('/tracking/store', [TrackingController::class, 'storeLocation'])->name('driver.tracking.storeLocation');
// In routes/web.php
Route::post('/tracking/{token}/location', [TrackingController::class, 'storeLocation'])->name('tracking.location');
 });