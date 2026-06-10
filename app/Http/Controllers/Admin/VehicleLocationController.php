<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FleetLocation;

class VehicleLocationController extends Controller
{
     public function index()
    {
        // Get latest location per vehicle
        $locations = FleetLocation::with('vehicle')
            ->latest()
            ->get()
            ->groupBy('vehicle_id')
            ->map(function ($group) {
                return $group->first(); // latest per vehicle
            })
            ->values();

        return view('admin.vehicle_locations.index', compact('locations'));
    }
}
