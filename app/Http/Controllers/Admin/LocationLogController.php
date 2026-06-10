<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\LocationLog;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class LocationLogController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with(['latestLocation', 'assignedDriver.user'])
                          ->latest('updated_at')
                          ->get();

        return view('admin.location-logs.index', compact('vehicles'));
    }

    public function generateLink(Vehicle $vehicle)
    {
        if (empty($vehicle->tracking_token)) {
            $vehicle->tracking_token = \Illuminate\Support\Str::random(60);
            $vehicle->tracking_token_expires_at = null;   // Never expires
            $vehicle->save();
        }

        $link = url('/tracking/' . $vehicle->tracking_token);

        return back()->with([
            'tracking_link'    => $link,
            'tracking_vehicle' => $vehicle->plate_number,
            'success'          => 'Permanent tracking link generated for ' . $vehicle->plate_number
        ]);
    }

    public function latestLocation(Vehicle $vehicle)
    {
        $latest = $vehicle->latestLocation;
        if (!$latest) return response()->json([]);

        return response()->json([
            'latitude'  => $latest->latitude,
            'longitude' => $latest->longitude,
            'accuracy'  => $latest->accuracy,
            'speed'     => $latest->speed,
            'timestamp' => $latest->created_at->toIso8601String()
        ]);
    }
}
