<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\LocationLog;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;  


class TrackingController extends Controller
{
   // Show tracking page (persistent)
    public function index($token)
    {
        $vehicle = Vehicle::where('tracking_token', $token)->first();

        if (!$vehicle) {
            return view('tracking.expired'); // or "invalid"
        }

        return view('tracking.dashboard', compact('vehicle'));
    }

    // Store location (called continuously)
    public function storeLocation(Request $request, $token)
    {
        $validated = $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy'  => 'nullable|numeric|min:0',
            'speed'     => 'nullable|numeric|min:0',
        ]);

        $vehicle = Vehicle::where('tracking_token', $token)->first();

        if (!$vehicle) {
            return response()->json(['success' => false, 'error' => 'Invalid token'], 403);
        }

        LocationLog::create([
            'vehicle_id' => $vehicle->id,
            'driver_id'  => $vehicle->assignedDriver?->id ?? null,
            'latitude'   => $validated['latitude'],
            'longitude'  => $validated['longitude'],
            'accuracy'   => $validated['accuracy'],
            'speed'      => $validated['speed'],
        ]);

        return response()->json(['success' => true]);
    }
}
