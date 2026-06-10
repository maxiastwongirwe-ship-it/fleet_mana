<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FleetLocation;
use App\Models\Vehicle;
use Illuminate\Support\Str;

class AdminLocationController extends Controller
{
        /**
     * Tracking links page
     */
    public function index()
    {
        $vehicles = Vehicle::latest()->get();

        return view('admin.locations.index', compact('vehicles'));
    }

    /**
     * Generate permanent tracking link
     */
    public function generateLink($id)
    {
        $vehicle = Vehicle::findOrFail($id);

        if (!$vehicle->tracking_token) {

            $vehicle->tracking_token = Str::uuid();

            $vehicle->save();
        }

        return back()->with(
            'success',
            'Tracking link generated successfully.'
        );
    }

    /**
     * Live map page
     */
    public function map()
    {
        return view('admin.locations.maps');
    }

    /**
     * Return latest locations
     */
    public function locations()
    {
        $locations = FleetLocation::with('vehicle')
            ->latest()
            ->get()
            ->unique('vehicle_id')
            ->values();

        return response()->json($locations);
    }
}
