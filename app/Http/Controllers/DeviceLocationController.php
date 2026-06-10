<?php

namespace App\Http\Controllers;


use App\Models\FleetLocation;
use App\Models\LocationLog;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class DeviceLocationController extends Controller
{
   public function track($token)
    {
        $vehicle = Vehicle::where('tracking_token', $token)->firstOrFail();

        return view('tracking.device', compact('vehicle'));
    }

    public function update(Request $request, $token)
    {
        $vehicle = Vehicle::where('tracking_token', $token)->firstOrFail();

        $validated = $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy'  => 'nullable|numeric',
            'speed'     => 'nullable|numeric',
        ]);

        // Save latest location (current position)
        FleetLocation::updateOrCreate(
            ['vehicle_id' => $vehicle->id],
            [
                'driver_id' => $vehicle->assigned_driver_id,
                'latitude'  => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'accuracy'  => $validated['accuracy'] ?? null,
                'speed'     => $validated['speed'] ?? null,
            ]
        );

        // Save history log
        LocationLog::create([
            'vehicle_id' => $vehicle->id,
            'driver_id'  => $vehicle->assigned_driver_id,
            'latitude'   => $validated['latitude'],
            'longitude'  => $validated['longitude'],
            'accuracy'   => $validated['accuracy'] ?? null,
            'speed'      => $validated['speed'] ?? null,
        ]);

        return response()->json([
            'success' => true
        ]);
    }
}
