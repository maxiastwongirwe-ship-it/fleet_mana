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
        $vehicles = Vehicle::with('latestLocation')->get();
        return view('admin.location-logs.index', compact('vehicles'));
    }

    public function generateLink(Vehicle $vehicle)
    {
        if (!$vehicle->assignedDriver) {
            return back()->with('error', 'No driver assigned to this vehicle yet.');
        }

        $token = Str::random(40);
        $vehicle->tracking_token = $token;
        $vehicle->tracking_token_expires_at = now()->addMinutes(3); // 3 minutes expiry
        $vehicle->save();

        $link = url('/tracking/' . $token);

        return back()->with([
            'tracking_link'       => $link,
            'tracking_expires_at' => $vehicle->tracking_token_expires_at->format('H:i:s'),
            'tracking_vehicle'    => $vehicle->plate_number,
        ]);
    }

    // Return latest location for AJAX live map
    public function latestLocation(Vehicle $vehicle)
    {
        $latest = $vehicle->latestLocation;
        if (!$latest) return response()->json([]);
        return response()->json([
            'latitude' => $latest->latitude,
            'longitude' => $latest->longitude,
            'accuracy' => $latest->accuracy,
            'speed' => $latest->speed
        ]);
    }
}
