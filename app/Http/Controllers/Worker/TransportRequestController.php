<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\TransportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransportRequestController extends Controller
{
//    public function __construct()
//     {
//         $this->middleware('auth');
//     }


        /**
     * Worker Dashboard
     */
    public function dashboard()
    {
        $userId = Auth::id();

        $totalRequests = TransportRequest::where('requested_by', $userId)->count();
        
        $pendingRequests = TransportRequest::where('requested_by', $userId)
            ->where('status', 'pending')
            ->count();

        $assignedRequests = TransportRequest::where('requested_by', $userId)
            ->whereIn('status', ['approved', 'assigned'])
            ->count();

        $recentRequests = TransportRequest::where('requested_by', $userId)
            ->with(['trips.vehicle', 'trips.driver'])
            ->latest()
            ->take(5)
            ->get();

        return view('worker.dashboard', compact(
            'totalRequests',
            'pendingRequests',
            'assignedRequests',
            'recentRequests'
        ));
    }

    /**
     * Display a listing of the worker's transport requests
     */
    public function index()
    {
        $requests = TransportRequest::where('requested_by', Auth::id())
            ->with(['passengers', 'trips.vehicle', 'trips.driver'])
            ->latest()
            ->paginate(12);

        return view('worker.transport-requests.index', compact('requests'));
    }

    /**
     * Show the form for creating a new request
     */
    public function create()
    {
        return view('worker.transport-requests.create');
    }

    /**
     * Store a newly created transport request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_type'      => 'required|in:passenger,goods',
            'pickup_location'   => 'required|string|max:255',
            'pickup_lat'        => 'nullable|numeric|between:-90,90',
            'pickup_lng'        => 'nullable|numeric|between:-180,180',
            'dropoff_location'  => 'required|string|max:255',
            'dropoff_lat'       => 'nullable|numeric|between:-90,90',
            'dropoff_lng'       => 'nullable|numeric|between:-180,180',
            'pickup_time'       => 'required|date|after:now',
            'purpose'           => 'nullable|string|max:1000',
            'passengers'        => 'required_if:request_type,passenger|array',
            'passengers.*.name' => 'required_if:request_type,passenger|string|max:255',
        ]);

        $transportRequest = TransportRequest::create([
            'request_type'     => $validated['request_type'],
            'requested_by'     => Auth::id(),
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

        // Add other passengers if it's a passenger request
        if ($validated['request_type'] === 'passenger' && !empty($validated['passengers'])) {
            foreach ($validated['passengers'] as $p) {
                $transportRequest->passengers()->create([
                    'passenger_name' => $p['name'],
                    'user_id'        => null,
                ]);
            }
        }

        // Always add the requester as the first passenger
        if ($validated['request_type'] === 'passenger') {
            $transportRequest->passengers()->create([
                'passenger_name' => Auth::user()->name,
                'user_id'        => Auth::id(),
            ]);
        }

        return redirect()->route('worker.transport-requests.index')
            ->with('success', 'Transport request submitted successfully! Awaiting admin approval.');
    }

    /**
     * Display the specified transport request
     */
  public function show(TransportRequest $transportRequest)
{
    if ($transportRequest->requested_by !== Auth::id()) {
        abort(403, 'You can only view your own requests.');
    }

    // Always load trips, vehicle, and driver
    $transportRequest->load([
        'passengers',
        'trips.vehicle',
        'trips.driver',
        'trips.vehicle.assignedDriver'
    ]);

    return view('worker.transport-requests.show', compact('transportRequest'));
}

    /**
     * Show the form for editing the transport request
     * Only allowed for pending requests
     */
    public function edit(TransportRequest $transportRequest)
    {
        if ($transportRequest->requested_by !== Auth::id()) {
            abort(403, 'You can only edit your own requests.');
        }

        if (!$transportRequest->isPending()) {
            return redirect()->route('worker.transport-requests.show', $transportRequest)
                ->with('error', 'Only pending requests can be edited.');
        }

        return view('worker.transport-requests.edit', compact('transportRequest'));
    }

    /**
     * Update the transport request
     */
    public function update(Request $request, TransportRequest $transportRequest)
    {
        if ($transportRequest->requested_by !== Auth::id()) {
            abort(403, 'You can only update your own requests.');
        }

        if (!$transportRequest->isPending()) {
            return redirect()->route('worker.transport-requests.show', $transportRequest)
                ->with('error', 'Only pending requests can be updated.');
        }

        $validated = $request->validate([
            'pickup_location'   => 'required|string|max:255',
            'pickup_lat'        => 'nullable|numeric|between:-90,90',
            'pickup_lng'        => 'nullable|numeric|between:-180,180',
            'dropoff_location'  => 'required|string|max:255',
            'dropoff_lat'       => 'nullable|numeric|between:-90,90',
            'dropoff_lng'       => 'nullable|numeric|between:-180,180',
            'pickup_time'       => 'required|date|after:now',
            'purpose'           => 'nullable|string|max:1000',
        ]);

        $transportRequest->update([
            'pickup_location'  => $validated['pickup_location'],
            'pickup_lat'       => $validated['pickup_lat'] ?? null,
            'pickup_lng'       => $validated['pickup_lng'] ?? null,
            'dropoff_location' => $validated['dropoff_location'],
            'dropoff_lat'      => $validated['dropoff_lat'] ?? null,
            'dropoff_lng'      => $validated['dropoff_lng'] ?? null,
            'pickup_time'      => $validated['pickup_time'],
            'purpose'          => $validated['purpose'] ?? null,
        ]);

        return redirect()->route('worker.transport-requests.show', $transportRequest)
            ->with('success', 'Transport request updated successfully.');
    }

    /**
     * Remove the transport request (Soft delete not used - hard delete for pending only)
     */
    public function destroy(TransportRequest $transportRequest)
    {
        if ($transportRequest->requested_by !== Auth::id()) {
            abort(403, 'You can only delete your own requests.');
        }

        if (!$transportRequest->isPending()) {
            return redirect()->route('worker.transport-requests.index')
                ->with('error', 'Only pending requests can be deleted.');
        }

        // Delete associated passengers
        $transportRequest->passengers()->delete();

        $transportRequest->delete();

        return redirect()->route('worker.transport-requests.index')
            ->with('success', 'Transport request deleted successfully.');
    }

        /**
     * Show the assigned vehicle and trip details for the worker
     */
    public function showVehicle(TransportRequest $transportRequest)
    {
        if ($transportRequest->requested_by !== Auth::id()) {
            abort(403, 'You can only view your own requests.');
        }

        if (!$transportRequest->isAssigned() && !$transportRequest->isApproved()) {
            return redirect()->route('worker.transport-requests.show', $transportRequest)
                ->with('error', 'No vehicle has been assigned to this request yet.');
        }

        $transportRequest->load([
            'trips.vehicle.assignedDriver',
            'trips.driver',
            'passengers'
        ]);

        return view('worker.transport-requests.vehicle', compact('transportRequest'));
    }
}
