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
use App\Http\Controllers\Admin\VehicleLocationController;

use App\Http\Controllers\Admin\AdminLocationController;
use App\Http\Controllers\DeviceLocationController;


use App\Http\Controllers\Admin\FleetLocationController;
use App\Http\Controllers\Driver\FleetTrackingController;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Laravel\Jetstream\Jetstream;

Route::get('/', function () {
    return view('layouts.guest'); // or any guest page
});
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
 Route::resource('workers', \App\Http\Controllers\Admin\WorkerController::class)
         ->names('admin.workers');
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

// Fuel Logs Routes

       // Fuel Logs - CORRECTED
Route::resource('fuel-logs', \App\Http\Controllers\Admin\FuelLogController::class)
     ->only(['index', 'create', 'store', 'show'])
     ->names('admin.fuel-logs');


    // Transport Requests
    Route::resource('transport-requests', TransportRequestController::class)->names('admin.transport-requests');
    Route::post('transport-requests/{transport_request}/approve', [TransportRequestController::class, 'approve'])->name('admin.transport-requests.approve');
    Route::post('transport-requests/{transport_request}/reject', [TransportRequestController::class, 'reject'])->name('admin.transport-requests.reject');
    Route::post('transport-requests/{transport_request}/assign', [TransportRequestController::class, 'assign'])->name('admin.transport-requests.assign');
  Route::get('/fleet', [FleetLocationController::class, 'index'])
         ->name('admin.fleet.index');

    // Generate Permanent Link
Route::get(
            '/tracking-links',
            [AdminLocationController::class, 'index']
        )->name('admin.tracking.links');

        Route::post(
            '/tracking-links/{id}',
            [AdminLocationController::class, 'generateLink']
        )->name('admin.tracking.generate');

        Route::get(
            '/tracking-map',
            [AdminLocationController::class, 'map']
        )->name('admin.tracking.map');

        Route::get(
            '/tracking-map/locations',
            [AdminLocationController::class, 'locations']
        )->name('admin.tracking.locations');

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

// Route::get('/admin/fleet', [FleetLocationController::class, 'index']);
// Route::post('/admin/generate-link/{driver}', [FleetLocationController::class, 'generateLink']);
// Route::get('/admin/fleet/{vehicle}', [FleetLocationController::class, 'show']);

// Route::get('/driver-track/{token}', [FleetTrackingController::class, 'index']);
// Route::post('/driver-track/{token}', [FleetTrackingController::class, 'store']);


    

// ────────────────────────────────────────────────
// Public Tracking Route
// ────────────────────────────────────────────────

// ────────────────────────────────────────────────
// NEW FLEET TRACKING ROUTES (Outside any group)
// ────────────────────────────────────────────────

// Dashboard (map + drivers)
Route::get('/admin/fleet', [FleetLocationController::class, 'index'])->name('admin.fleet.index');
// Generate tracking link
Route::post('/admin/generate-link/{driver}', [FleetLocationController::class, 'generateLink']);

// View single vehicle tracking
Route::get('/admin/fleet/{vehicle}', [FleetLocationController::class, 'show']);


// Open tracking page (from generated link)
Route::get('/driver-track/{token}', [FleetTrackingController::class, 'index']);

// Receive GPS data from driver device
Route::post('/driver-track/{token}', [FleetTrackingController::class, 'store']);


// Admin page
// Admin page
Route::get('/admin/vehicle-locations', [VehicleLocationController::class, 'index'])
    ->name('admin.vehicle-locations.index');

// ────────────────────────────────────────────────
// Driver Routes
// ────────────────────────────────────────────────
// ====================== DRIVER ROUTES ======================
Route::prefix('driver')
    ->middleware(['auth', 'role:driver'])
    ->name('driver.')           // ← This is the most important line
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Driver\TripController::class, 'dashboard'])
             ->name('dashboard');

        // Trips
        Route::get('trips', [\App\Http\Controllers\Driver\TripController::class, 'index'])
             ->name('trips.index');

        Route::get('trips/{trip}', [\App\Http\Controllers\Driver\TripController::class, 'show'])
             ->name('trips.show');

        Route::post('trips/{trip}/start', [\App\Http\Controllers\Driver\TripController::class, 'startTrip'])
             ->name('trips.start');

        Route::post('trips/{trip}/finish', [\App\Http\Controllers\Driver\TripController::class, 'finishTrip'])
             ->name('trips.finish');

        // Fuel Requests (keep your existing ones)
        Route::get('fuel-requests', [DriverFuelRequestController::class, 'index'])
             ->name('fuel-requests.index');

        Route::get('fuel-requests/create', [DriverFuelRequestController::class, 'create'])
             ->name('fuel-requests.create');

        Route::post('fuel-requests', [DriverFuelRequestController::class, 'store'])
             ->name('fuel-requests.store');

        Route::get('fuel-requests/{fuelRequest}', [DriverFuelRequestController::class, 'show'])
             ->name('fuel-requests.show');

        Route::get('fuel-requests/{fuelRequest}/complete', [DriverFuelRequestController::class, 'complete'])
             ->name('fuel-requests.complete');

        Route::post('fuel-requests/{fuelRequest}/complete', [DriverFuelRequestController::class, 'storeCompletion'])
             ->name('fuel-requests.store-completion');

        Route::post('fuel-requests/{fuelRequest}/request-payment', [DriverFuelRequestController::class, 'requestPayment'])
             ->name('fuel-requests.request-payment');

        // Tracking
        Route::get('/tracking/{token}', [TrackingController::class, 'index'])
             ->name('tracking.index');

        Route::post('/tracking/store', [TrackingController::class, 'storeLocation'])
             ->name('tracking.storeLocation');


             // Breakdowns
Route::get('breakdowns/create', [\App\Http\Controllers\Driver\BreakdownController::class, 'create'])
     ->name('breakdowns.create');

Route::post('breakdowns', [\App\Http\Controllers\Driver\BreakdownController::class, 'store'])
     ->name('breakdowns.store');

Route::get('breakdowns', [\App\Http\Controllers\Driver\BreakdownController::class, 'index'])
     ->name('breakdowns.index');

Route::get('breakdowns/{breakdown}', [\App\Http\Controllers\Driver\BreakdownController::class, 'show'])
     ->name('breakdowns.show');

    // Correct - Use the full Driver controller namespace
Route::post('breakdowns/{breakdown}/mark-repaired', 
    [\App\Http\Controllers\Driver\BreakdownController::class, 'markRepaired'])
    ->name('breakdowns.mark-repaired');
    });

 use App\Models\FleetLocation;


Route::get('/api/fleet-locations', function () {

    return FleetLocation::latest()
        ->get()
        ->groupBy('vehicle_id')
        ->map(function ($group) {
            return $group->first(); // latest per vehicle
        })
        ->values();

});


// 🔥 Data endpoint for map (NO API CONTROLLER)
Route::get('/vehicle-locations-data', function () {

    return FleetLocation::with('vehicle')
        ->latest()
        ->get()
        ->groupBy('vehicle_id')
        ->map(function ($group) {
            return $group->first();
        })
        ->values();

});
// ====================== WORKER ROUTES ======================
Route::prefix('worker')
    ->name('worker.')
    ->middleware(['auth', 'role:worker'])   // Add role middleware if you have it
    ->group(function () {

        Route::get('/dashboard', [\App\Http\Controllers\Worker\TransportRequestController::class, 'dashboard'])
             ->name('dashboard');

        // Full Resource Routes
        Route::resource('transport-requests', \App\Http\Controllers\Worker\TransportRequestController::class);

        // View assigned vehicle
        Route::get('transport-requests/{transport_request}/vehicle', 
            [\App\Http\Controllers\Worker\TransportRequestController::class, 'showVehicle'])
            ->name('transport-requests.vehicle');
    });

Route::get(
    '/track-device/{token}',
    [DeviceLocationController::class, 'track']
)->name('device.track');

Route::post(
    '/track-device/{token}/update',
    [DeviceLocationController::class, 'update']
)->name('device.track.update');