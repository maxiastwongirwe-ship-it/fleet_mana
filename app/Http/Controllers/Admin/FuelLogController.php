<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FuelLog;
use App\Models\FuelRequest;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;

class FuelLogController extends Controller
{
 public function index(Request $request)
    {
        $query = FuelRequest::with(['requester', 'vehicle', 'approvedBy', 'fuelLog'])
            ->orderByDesc('requested_at');

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        $logs = $query->paginate(15);
        $vehicles = \App\Models\Vehicle::orderBy('plate_number')->get();

        return view('admin.fuel-logs.index', compact('logs', 'vehicles'));
    }

    public function create()
    {
        $vehicles = Vehicle::with(['fuelLogs' => function ($q) {
            $q->orderByDesc('filled_at')->take(5);
        }])->orderBy('plate_number')->get();
        
        $drivers = User::where('role', 'driver')
                       ->orderBy('name')
                       ->get();

        $approvedRequests = FuelRequest::where('status', 'approved')
            ->whereDoesntHave('fuelLog')
            ->with(['vehicle.assignedDriver', 'requester'])
            ->get();

        return view('admin.fuel-logs.create', compact('vehicles', 'drivers', 'approvedRequests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fuel_request_id'   => ['nullable', 'exists:fuel_requests,id'],
            'vehicle_id'        => ['required_without:fuel_request_id', 'nullable', 'exists:vehicles,id'],
            'driver_id'         => ['required_without:fuel_request_id', 'nullable', 'exists:users,id'],
            'litres_dispensed'  => ['required', 'numeric', 'min:0.1'],
            'odometer_reading'  => ['required', 'integer', 'min:0'],
            'fuel_type'         => ['nullable', 'string', 'max:50'],
            'station_name'      => ['nullable', 'string', 'max:150'],
            'total_cost'        => ['nullable', 'numeric', 'min:0'],
            'payment_method'    => ['nullable', 'string', 'max:50'],
            'notes'             => ['nullable', 'string', 'max:1000'],
            'odometer_photo'    => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'receipt_photo'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
        ]);

        $data = $validated;

        if ($request->filled('fuel_request_id')) {
            $fuelRequest = FuelRequest::with('vehicle.assignedDriver')->findOrFail($validated['fuel_request_id']);

            if ($fuelRequest->status !== 'approved') {
                return back()->withInput()->withErrors(['fuel_request_id' => 'Selected fuel request must be approved before saving a fuel log.']);
            }

            if ($fuelRequest->fuelLog) {
                return back()->withInput()->withErrors(['fuel_request_id' => 'This fuel request already has a fuel log attached.']);
            }

            $data['vehicle_id'] = $fuelRequest->vehicle_id;
            $data['driver_id'] = $data['driver_id'] ?? $fuelRequest->vehicle?->assigned_driver_id;
            $data['fuel_type'] = $data['fuel_type'] ?? $fuelRequest->fuel_type;
            $data['station_name'] = $data['station_name'] ?? $fuelRequest->station_name;
            $data['total_cost'] = $data['total_cost'] ?? $fuelRequest->total_cost;
            $data['payment_method'] = $data['payment_method'] ?? $fuelRequest->payment_method;
            $data['notes'] = $data['notes'] ?? $fuelRequest->fillup_notes;
        }

        $data['logged_by'] = auth()->id();
        $data['filled_at'] = now();

        // Handle Photos
        if ($request->hasFile('odometer_photo')) {
            $data['odometer_photo_path'] = $request->file('odometer_photo')
                ->store('fuel-odometer-photos', 'public');
        }

        if ($request->hasFile('receipt_photo')) {
            $data['receipt_photo_path'] = $request->file('receipt_photo')
                ->store('fuel-receipt-photos', 'public');
        }

        // Create the record
        $fuelLog = FuelLog::create($data);

        // Update Fuel Request status when linked
        if (!empty($fuelRequest)) {
            $fuelRequest->update(['status' => 'completed']);
        }

        // Fuel Theft Detection
        $isSuspicious = $fuelLog->isSuspicious(1.25);

        $message = 'Fuel fill-up logged successfully.';

        if ($isSuspicious) {
            $message .= " ⚠️ HIGH CONSUMPTION DETECTED!";
        }

        return redirect()
            ->route('admin.fuel-logs.index')
            ->with('success', $message);
    }

    public function show(FuelLog $fuelLog)
    {
        $fuelLog->load(['vehicle', 'driver', 'loggedBy', 'fuelRequest']);

        $previousLogs = FuelLog::where('vehicle_id', $fuelLog->vehicle_id)
            ->where('id', '<', $fuelLog->id)
            ->orderByDesc('filled_at')
            ->take(5)
            ->get();

        return view('admin.fuel-logs.show', compact('fuelLog', 'previousLogs'));
    }
}
