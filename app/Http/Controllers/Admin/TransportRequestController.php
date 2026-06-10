<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransportRequest;
use App\Models\TransportTrip;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;

class TransportRequestController extends Controller
{
    public function __construct()
    {
       
    }

   public function index()
    {
        $requests = TransportRequest::with([
            'requester',
            'passengers.user',
            'trips.vehicle',
            'trips.driver'
        ])
        ->latest()
        ->paginate(15);

        return view('admin.transport-requests.index', compact('requests'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('admin.transport-requests.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_type'      => 'required|in:passenger,goods',
            'requested_by'      => 'required|exists:users,id',
            'pickup_location'   => 'required|string|max:255',
            'pickup_lat'        => 'nullable|numeric|between:-90,90',
            'pickup_lng'        => 'nullable|numeric|between:-180,180',
            'dropoff_location'  => 'required|string|max:255',
            'dropoff_lat'       => 'nullable|numeric|between:-90,90',
            'dropoff_lng'       => 'nullable|numeric|between:-180,180',
            'pickup_time'       => 'required|date',
            'purpose'           => 'nullable|string',
            'passengers'        => 'required_if:request_type,passenger|array',
            'passengers.*.name' => 'required_if:request_type,passenger|string|max:255',
            'passengers.*.user_id' => 'nullable|exists:users,id',
        ]);

        $transportRequest = TransportRequest::create([
            'request_type'     => $validated['request_type'],
            'requested_by'     => $validated['requested_by'],
            'pickup_location'  => $validated['pickup_location'],
            'pickup_lat'       => $validated['pickup_lat'] ?? null,
            'pickup_lng'       => $validated['pickup_lng'] ?? null,
            'dropoff_location' => $validated['dropoff_location'],
            'dropoff_lat'      => $validated['dropoff_lat'] ?? null,
            'dropoff_lng'      => $validated['dropoff_lng'] ?? null,
            'pickup_time'      => $validated['pickup_time'],
            'purpose'          => $validated['purpose'] ?? null,
            'status'           => 'pending',
        ]);

        if ($request->request_type === 'passenger' && $request->passengers) {
            foreach ($request->passengers as $p) {
                $transportRequest->passengers()->create([
                    'passenger_name' => $p['name'],
                    'user_id'        => $p['user_id'] ?? null,
                ]);
            }
        }

        return redirect()->route('admin.transport-requests.index')
            ->with('success', 'Transport request created successfully.');
    }

       public function show(TransportRequest $transportRequest)
    {
        $transportRequest->load([
            'requester',
            'passengers.user',
            'trips.vehicle',
            'trips.driver'
        ]);

        $availableVehicles = Vehicle::where('status', '!=', 'maintenance')
            ->whereDoesntHave('transportTrips', function ($q) {
                $q->whereIn('status', ['scheduled', 'active']);
            })
            ->get();

        $availableDrivers = User::where('role', 'driver')
            ->whereDoesntHave('transportTripsAsDriver', function ($q) {
                $q->whereIn('status', ['scheduled', 'active']);
            })
            ->get();

        return view('admin.transport-requests.show', compact(
            'transportRequest',
            'availableVehicles',
            'availableDrivers'
        ));
    }

    // Approve
    public function approve(TransportRequest $transportRequest)
    {
        if (!$transportRequest->isPending()) {
            return back()->with('error', 'Only pending requests can be approved.');
        }

        $transportRequest->update(['status' => 'approved']);

        return back()->with('success', 'Request approved successfully.');
    }

    // Reject
    public function reject(Request $request, TransportRequest $transportRequest)
    {
        if (!$transportRequest->isPending()) {
            return back()->with('error', 'Only pending requests can be rejected.');
        }

        $validated = $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        $transportRequest->update([
            'status'      => 'rejected',
            'admin_notes' => $validated['admin_notes'],
        ]);

        return back()->with('success', 'Request rejected.');
    }

    // Assign vehicle + driver
       /**
     * Assign vehicle (and driver) to the transport request
     */
    public function assign(Request $request, TransportRequest $transportRequest)
    {
        if (!in_array($transportRequest->status, ['pending', 'approved'])) {
            return back()->with('error', 'This request cannot be assigned. It must be pending or approved first.');
        }

        $validated = $request->validate([
            'vehicle_id'     => 'required|exists:vehicles,id',
            'driver_id'      => 'nullable|exists:users,id,role,driver',
            'departure_time' => 'required|date|after:now',
        ]);

        $vehicle = Vehicle::with('assignedDriver')->findOrFail($validated['vehicle_id']);

        if (!$vehicle->isAvailable()) {
            return back()->with('error', 'This vehicle is not available.');
        }

        $driver_id = $validated['driver_id'] ?? $vehicle->assigned_driver_id;

        if (!$driver_id) {
            return back()->with('error', 'No driver found for this vehicle.');
        }

        // Check driver is not busy
        $busyDriver = TransportTrip::where('driver_id', $driver_id)
            ->whereIn('status', ['scheduled', 'active'])
            ->exists();

        if ($busyDriver) {
            return back()->with('error', 'This driver is already assigned to another active trip.');
        }

        // Create trip
        $trip = TransportTrip::create([
            'vehicle_id'     => $vehicle->id,
            'driver_id'      => $driver_id,
            'departure_time' => $validated['departure_time'],
            'status'         => 'scheduled',
            'notes'          => 'Assigned to transport request #' . $transportRequest->id,
        ]);

        // Link request to trip
        $transportRequest->trips()->attach($trip->id);

        // IMPORTANT: Force update status
        $transportRequest->update(['status' => 'assigned']);

        // Refresh the model to get latest relationships
        $transportRequest->refresh();
        $transportRequest->load(['trips.vehicle', 'trips.driver']);

        return back()->with('success', "Vehicle {$vehicle->plate_number} has been successfully assigned.");
    }
}
