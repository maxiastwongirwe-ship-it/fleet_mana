<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\FleetLocation;
use Illuminate\Http\Request;

class FleetTrackingController extends Controller
{
     public function index($token)
    {
        $driver = Driver::where('tracking_token', $token)->first();

        if (!$driver || !$driver->isTrackingTokenValid()) {
            return view('tracking.expired');
        }

        return view('tracking.driver', compact('driver', 'token'));
    }

    public function store(Request $request, $token)
    {
        $driver = Driver::where('tracking_token', $token)->first();

        if (!$driver || !$driver->isTrackingTokenValid()) {
            return response()->json(['error' => 'Invalid token'], 403);
        }

        $data = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
            'speed' => 'nullable|numeric',
        ]);

        FleetLocation::create([
            'driver_id' => $driver->user_id,
            'vehicle_id' => $driver->user->assignedVehicle?->id,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'accuracy' => $data['accuracy'] ?? null,
            'speed' => $data['speed'] ?? null,
        ]);

        return response()->json(['success' => true]);
    }
}
