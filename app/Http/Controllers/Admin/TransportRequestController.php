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
            'dropoff_location'  => 'required|string|max:255',
            'pickup_time'       => 'required|date',
            'purpose'           => 'nullable|string',
            'passengers'        => 'required_if:request_type,passenger|array',
            'passengers.*.name' => 'required_if:request_type,passenger|string|max:255',
            'passengers.*.user_id' => 'nullable|exists:users,id',
        ]);

        $transportRequest = TransportRequest::create($validated);

        if ($request->request_type === 'passenger' && $request->passengers) {
            foreach ($request->passengers as $p) {
                $transportRequest->passengers()->create([
                    'passenger_name' => $p['name'],
                    'user_id'        => $p['user_id'] ?? null,
                ]);
            }
        }

        return redirect()->route('admin.transport-requests.index')
            ->with('success', 'Transport request created.');
    }

    public function show(TransportRequest $transportRequest)
    {
        $transportRequest->load([
            'requester',
            'passengers.user',
            'trips.vehicle',
            'trips.driver'
        ]);

        // Available vehicles (not in active/scheduled trip)
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

    // Approve pending request
    public function approve(TransportRequest $transportRequest)
    {
        if (!$transportRequest->isPending()) {
            return back()->with('error', 'Only pending requests can be approved.');
        }

        $transportRequest->update(['status' => 'approved']);

        return back()->with('success', 'Request approved.');
    }

    // Reject with reason
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

    // Assign vehicle + driver (creates trip)
    public function assign(Request $request, TransportRequest $transportRequest)
    {
        if (!in_array($transportRequest->status, ['pending', 'approved'])) {
            return back()->with('error', 'This request cannot be assigned.');
        }

        $validated = $request->validate([
            'vehicle_id'     => 'required|exists:vehicles,id',
            'driver_id'      => 'required|exists:users,id,role,driver',
            'departure_time' => 'required|date|after:now',
        ]);

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        if ($vehicle->status === 'maintenance') {
            return back()->with('error', 'Vehicle is under maintenance.');
        }

        // Create trip
        $trip = TransportTrip::create([
            'vehicle_id'     => $validated['vehicle_id'],
            'driver_id'      => $validated['driver_id'],
            'departure_time' => $validated['departure_time'],
            'status'         => 'scheduled',
        ]);

        // Link request to trip
        $transportRequest->trips()->attach($trip->id);

        // Update status
        $transportRequest->update(['status' => 'assigned']);

        return back()->with('success', 'Vehicle & driver assigned.');
    }
}
