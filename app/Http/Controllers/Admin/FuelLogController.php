<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FuelLog;
use App\Models\FuelRequest;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FuelLogController extends Controller
{public function __construct()
    {
        
    }

    public function index(Request $request)
    {
        $query = FuelLog::with(['vehicle', 'driver', 'loggedBy', 'fuelRequest'])
            ->orderByDesc('filled_at');

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        $logs = $query->paginate(15);

        $vehicles = Vehicle::orderBy('plate_number')->get();

        return view('admin.fuel-logs.index', compact('logs', 'vehicles'));
    }

    public function create()
    {
        $vehicles = Vehicle::orderBy('plate_number')->get();
        $drivers = User::where('role', 'driver')->orderBy('name')->get();
        $approvedRequests = FuelRequest::where('status', 'approved')
            ->whereDoesntHave('fuelLog')
            ->get();

        return view('admin.fuel-logs.create', compact('vehicles', 'drivers', 'approvedRequests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fuel_request_id'       => ['nullable', 'exists:fuel_requests,id'],
            'vehicle_id'            => ['required', 'exists:vehicles,id'],
            'driver_id'             => ['required', 'exists:users,id'],
            'litres_dispensed'      => ['required', 'numeric', 'min:0.1'],
            'odometer_reading'      => ['required', 'integer', 'min:0'],
            'fuel_type'             => ['nullable', 'string', 'max:50'],
            'station_name'          => ['nullable', 'string', 'max:150'],
            'total_cost'            => ['nullable', 'numeric', 'min:0'],
            'payment_method'        => ['nullable', 'string', 'max:50'],
            'notes'                 => ['nullable', 'string'],
            'odometer_photo'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'receipt_photo'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
        ]);

        // Enforce odometer > previous
        $lastLog = FuelLog::where('vehicle_id', $request->vehicle_id)
                          ->orderByDesc('filled_at')
                          ->first();

        if ($lastLog && $request->odometer_reading <= $lastLog->odometer_reading) {
            return back()->withErrors(['odometer_reading' => 'Odometer reading must be greater than the previous log (' . $lastLog->odometer_reading . ' km)']);
        }

        $data = $validated;
        $data['logged_by'] = auth()->id();

        // Photos
        if ($request->hasFile('odometer_photo')) {
            $data['odometer_photo_path'] = $request->file('odometer_photo')->store('fuel-odometer-photos', 'public');
        }
        if ($request->hasFile('receipt_photo')) {
            $data['receipt_photo_path'] = $request->file('receipt_photo')->store('fuel-receipt-photos', 'public');
        }

        FuelLog::create($data);

        // Optional: mark linked request as completed
        if ($request->fuel_request_id) {
            FuelRequest::where('id', $request->fuel_request_id)
                ->update(['status' => 'completed']);
        }

        return redirect()->route('admin.fuel-logs.index')
            ->with('success', 'Fuel fill-up logged successfully.');
    }

    public function show(FuelLog $fuelLog)
    {
        $fuelLog->load(['vehicle', 'driver', 'loggedBy', 'fuelRequest']);

        // Get last 5 logs for this vehicle
        $previousLogs = FuelLog::where('vehicle_id', $fuelLog->vehicle_id)
            ->where('id', '<', $fuelLog->id)
            ->orderByDesc('filled_at')
            ->take(5)
            ->get();

        return view('admin.fuel-logs.show', compact('fuelLog', 'previousLogs'));
    }
}
