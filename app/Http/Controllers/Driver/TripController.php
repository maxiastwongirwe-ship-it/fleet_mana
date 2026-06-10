<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransportTrip;
use Illuminate\Support\Facades\Auth;

class TripController extends Controller
{
    

    /**
     * Driver Dashboard
     */
    public function dashboard()
    {
        $driverId = Auth::id();

        $activeTrips = TransportTrip::where('driver_id', $driverId)
            ->where('status', 'active')
            ->count();

        $upcomingTrips = TransportTrip::where('driver_id', $driverId)
            ->where('status', 'scheduled')
            ->count();

        $completedToday = TransportTrip::where('driver_id', $driverId)
            ->where('status', 'completed')
            ->whereDate('actual_arrival_time', today())
            ->count();

        $totalTrips = TransportTrip::where('driver_id', $driverId)->count();

        // Active + Upcoming trips for display
        $activeTripsList = TransportTrip::where('driver_id', $driverId)
            ->whereIn('status', ['scheduled', 'active'])
            ->with([
                'vehicle',
                'requests.requester',
                'requests.passengers'
            ])
            ->latest()
            ->take(5)
            ->get();

        return view('driver.dashboard', compact(
            'activeTrips',
            'upcomingTrips',
            'completedToday',
            'totalTrips',
            'activeTripsList'
        ));
    }

    /**
     * List all trips assigned to this driver
     */
    public function index()
    {
        $trips = TransportTrip::where('driver_id', Auth::id())
            ->with([
                'vehicle',
                'requests.requester',
                'requests.passengers'
            ])
            ->latest()
            ->paginate(10);

        return view('driver.trips.index', compact('trips'));
    }

    /**
     * Show single trip with map and details
     */
    public function show(TransportTrip $trip)
    {
        if ($trip->driver_id !== Auth::id()) {
            abort(403, 'This trip is not assigned to you.');
        }

        $trip->load([
            'vehicle',
            'requests' => function ($q) {
                $q->with(['requester', 'passengers']);
            }
        ]);

        return view('driver.trips.show', compact('trip'));
    }

    /**
     * Driver starts the trip
     */
    public function startTrip(TransportTrip $trip)
    {
        if ($trip->driver_id !== Auth::id()) {
            abort(403);
        }

        if (!$trip->canStartTrip()) {
            return back()->with('error', 'This trip cannot be started at this time.');
        }

        $trip->update([
            'status' => 'active',
            'departure_time' => now(),   // Record actual start time
        ]);

        return back()->with('success', 'Trip started successfully. Safe journey!');
    }

    /**
     * Driver finishes the trip
     */
    public function finishTrip(Request $request, TransportTrip $trip)
    {
        if ($trip->driver_id !== Auth::id()) {
            abort(403);
        }

        if (!$trip->canFinishTrip()) {
            return back()->with('error', 'Only active trips can be marked as completed.');
        }

        $validated = $request->validate([
            'actual_arrival_time' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $trip->update([
            'status' => 'completed',
            'actual_arrival_time' => $validated['actual_arrival_time'],
            'notes' => $validated['notes'] ?? $trip->notes,
        ]);

        return back()->with('success', 'Trip marked as completed. Thank you for your service!');
    }
}
