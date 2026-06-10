<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Breakdown;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;

class BreakdownController extends Controller
{
 
    /**
     * Show form to report a new breakdown
     */
    public function create()
    {
        $vehicles = Vehicle::where('assigned_driver_id', Auth::id())
            ->orWhereHas('transportTrips', function ($q) {
                $q->where('driver_id', Auth::id())
                  ->whereIn('status', ['scheduled', 'active']);
            })
            ->get();

        return view('driver.breakdowns.create', compact('vehicles'));
    }

    /**
     * Store the breakdown report
     */
        public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id'      => 'required|exists:vehicles,id',
            'location'        => 'nullable|string|max:255',
            'description'     => 'required|string|min:10',
            'occurred_at'     => 'required|date',
            'severity'        => 'required|in:minor,moderate,major,critical',
            'estimated_cost'  => 'required|numeric|min:0',
            'photo'           => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('breakdowns', 'public');
        }

        Breakdown::create([
            'vehicle_id'     => $validated['vehicle_id'],
            'driver_id'      => Auth::id(),
            'location'       => $validated['location'],
            'description'    => $validated['description'],
            'occurred_at'    => $validated['occurred_at'],
            'severity'       => $validated['severity'],
            'estimated_cost' => $validated['estimated_cost'],
            'status'         => 'reported',
            'photo_paths'    => $photoPath ? [$photoPath] : null,
        ]);

        return redirect()->route('driver.breakdowns.index')
            ->with('success', 'Breakdown reported successfully. Admin will review it soon.');
    }

    /**
     * List all breakdowns reported by this driver
     */
    public function index()
    {
        $breakdowns = Breakdown::where('driver_id', Auth::id())
            ->with(['vehicle'])
            ->latest()
            ->paginate(10);

        return view('driver.breakdowns.index', compact('breakdowns'));
    }

    /**
     * Show single breakdown details
     */
    public function show(Breakdown $breakdown)
    {
        if ($breakdown->driver_id !== Auth::id()) {
            abort(403, 'You can only view your own breakdown reports.');
        }

        $breakdown->load(['vehicle', 'approvedBy']);

        return view('driver.breakdowns.show', compact('breakdown'));
    }


        /**
     * Driver marks breakdown as repaired and requests payment
     */
   

            /**
     * Driver marks breakdown as repaired and requests payment
     */
    public function markRepaired(Request $request, Breakdown $breakdown)
    {
        if ($breakdown->driver_id !== Auth::id()) {
            abort(403, 'You can only manage your own breakdown reports.');
        }

        // Allow if approved by admin
        if (!$breakdown->approved_by && $breakdown->status !== 'acknowledged') {
            return back()->with('error', 'This breakdown must be approved by admin first.');
        }

        $validated = $request->validate([
            'actual_cost'     => 'nullable|numeric|min:0',
            'repair_photos.*' => 'required|image|mimes:jpeg,png,jpg|max:5120', // at least one photo required
        ]);

        $photoPaths = $breakdown->photo_paths ?? [];

        // Save repair photos
        if ($request->hasFile('repair_photos')) {
            foreach ($request->file('repair_photos') as $photo) {
                $path = $photo->store('breakdowns/repairs', 'public');
                $photoPaths[] = $path;
            }
        }

        // Update the breakdown
        $breakdown->update([
            'status'       => 'resolved',
            'actual_cost'  => $validated['actual_cost'] ?? $breakdown->estimated_cost,
            'photo_paths'  => array_values($photoPaths),   // re-index array
        ]);

        return redirect()->route('driver.breakdowns.index')
            ->with('success', 'Breakdown marked as repaired. Payment request has been sent to admin for approval.');
    }
}
