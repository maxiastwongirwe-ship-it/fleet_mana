<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\FleetLocation;
use Illuminate\Support\Str;

class FleetLocationController extends Controller
{
     public function index($token)
    {
        $vehicle = Vehicle::where('tracking_token', $token)->first();

        if (!$vehicle) {
            abort(404, 'Invalid or expired tracking link');
        }

        return view('tracking.dashboard', compact('vehicle'));
    }

    public function storeLocation(Request $request, $token)
    {
        $validated = $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy'  => 'nullable|numeric',
            'speed'     => 'nullable|numeric',
        ]);

        $vehicle = Vehicle::where('tracking_token', $token)->first();

        if (!$vehicle) {
            return response()->json(['success' => false, 'error' => 'Invalid token'], 404);
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
