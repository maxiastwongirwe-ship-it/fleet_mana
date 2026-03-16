<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Breakdown;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BreakdownController extends Controller
{
  public function index()
    {
        $breakdowns = Breakdown::with(['vehicle', 'driver'])
            ->orderByDesc('occurred_at')
            ->paginate(15);

        return view('admin.breakdowns.index', compact('breakdowns'));
    }

    public function create()
    {
        $vehicles = Vehicle::orderBy('plate_number')->get();
        $drivers  = User::where('role', 'driver')->orderBy('name')->get();

        return view('admin.breakdowns.create', compact('vehicles', 'drivers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id'     => ['required', 'exists:vehicles,id'],
            'driver_id'      => ['nullable', 'exists:users,id'],
            'location'       => ['nullable', 'string', 'max:255'],
            'description'    => ['required', 'string'],
            'occurred_at'    => ['required', 'date'],
            'severity'       => ['required', 'in:minor,moderate,major,critical'],
            'estimated_cost' => ['nullable', 'numeric', 'min:0'],
            'photos.*'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
        ]);

        $data = $validated;
        $data['status'] = 'reported';
        $data['approved_by'] = null;

        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                if ($photo->isValid()) {
                    $originalName = $photo->getClientOriginalName();
                    $extension    = $photo->getClientOriginalExtension();
                    $safeName     = Str::slug(pathinfo($originalName, PATHINFO_FILENAME), '-') . '.' . $extension;

                    // Make unique if file exists
                    $path = 'breakdown-photos/' . $safeName;
                    $counter = 1;
                    while (Storage::disk('public')->exists($path)) {
                        $path = 'breakdown-photos/' . pathinfo($safeName, PATHINFO_FILENAME) . '-' . $counter . '.' . $extension;
                        $counter++;
                    }

                    $photo->storeAs('breakdown-photos', basename($path), 'public');
                    $photoPaths[] = $path;

                    Log::info("Photo stored (original name preserved)", ['path' => $path]);
                }
            }
        }

        $data['photo_paths'] = $photoPaths;

        Breakdown::create($data);

        return redirect()->route('admin.breakdowns.index')
            ->with('success', 'Breakdown reported successfully.');
    }

    public function show(Breakdown $breakdown)
    {
        $breakdown->load(['vehicle', 'driver', 'approvedBy']);
        return view('admin.breakdowns.show', compact('breakdown'));
    }

    public function edit(Breakdown $breakdown)
    {
        $breakdown->load(['vehicle', 'driver']);
        $vehicles = Vehicle::orderBy('plate_number')->get();
        $drivers  = User::where('role', 'driver')->orderBy('name')->get();

        return view('admin.breakdowns.edit', compact('breakdown', 'vehicles', 'drivers'));
    }

    public function update(Request $request, Breakdown $breakdown)
    {
        $validated = $request->validate([
            'vehicle_id'     => ['required', 'exists:vehicles,id'],
            'driver_id'      => ['nullable', 'exists:users,id'],
            'location'       => ['nullable', 'string', 'max:255'],
            'description'    => ['required', 'string'],
            'occurred_at'    => ['required', 'date'],
            'severity'       => ['required', 'in:minor,moderate,major,critical'],
            'status'         => ['required', 'in:reported,acknowledged,in_progress,resolved,rejected'],
            'estimated_cost' => ['nullable', 'numeric', 'min:0'],
            'actual_cost'    => ['nullable', 'numeric', 'min:0'],
            'admin_notes'    => ['nullable', 'string'],
            'photos.*'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
        ]);

        $data = $validated;

        $photoPaths = $breakdown->photo_paths ?? [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                if ($photo->isValid()) {
                    $originalName = $photo->getClientOriginalName();
                    $extension    = $photo->getClientOriginalExtension();
                    $safeName     = Str::slug(pathinfo($originalName, PATHINFO_FILENAME), '-') . '.' . $extension;

                    $path = 'breakdown-photos/' . $safeName;
                    $counter = 1;
                    while (Storage::disk('public')->exists($path)) {
                        $path = 'breakdown-photos/' . pathinfo($safeName, PATHINFO_FILENAME) . '-' . $counter . '.' . $extension;
                        $counter++;
                    }

                    $photo->storeAs('breakdown-photos', basename($path), 'public');
                    $photoPaths[] = $path;

                    Log::info("Added photo to breakdown #{$breakdown->id}", ['path' => $path]);
                }
            }
        }

        $data['photo_paths'] = array_values(array_filter($photoPaths));

        if (in_array($request->status, ['resolved', 'rejected'])) {
            $data['approved_by'] = auth()->id();
        }

        $breakdown->update($data);

        return redirect()->route('admin.breakdowns.index')
            ->with('success', 'Breakdown updated successfully.');
    }

    public function destroy(Breakdown $breakdown)
    {
        if ($breakdown->photo_paths) {
            foreach ($breakdown->photo_paths as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        $breakdown->delete();

        return redirect()->route('admin.breakdowns.index')
            ->with('success', 'Breakdown record deleted.');
    }


    public function approve(Breakdown $breakdown)
{
    if (!$breakdown->canBeApprovedBy(auth()->user())) {
        return back()->with('error', 'You do not have permission to approve this breakdown.');
    }

    $breakdown->update([
        'approved'     => true,
        'approved_by'  => auth()->id(),
        // Optional: also change status if you want
        // 'status'    => 'acknowledged',
    ]);

    return back()->with('success', 'Breakdown approved successfully.');
}
}
