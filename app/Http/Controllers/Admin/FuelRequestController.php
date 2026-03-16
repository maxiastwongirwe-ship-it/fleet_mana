<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FuelRequest;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FuelRequestController extends Controller
{
   public function index(Request $request)
    {
        $query = FuelRequest::with(['requester', 'vehicle', 'approvedBy'])
            ->orderByDesc('requested_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(15);

        return view('admin.fuel-requests.index', compact('requests'));
    }

    public function create()
    {
        $vehicles = Vehicle::orderBy('plate_number')->get();
        return view('admin.fuel-requests.create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id'          => ['required', 'exists:vehicles,id'],
            'requested_amount'    => ['required', 'numeric', 'min:0.01'],
            'fuel_type'           => ['required', 'in:Petrol,Diesel'],
            'reason'              => ['nullable', 'string', 'max:1000'],
            'odometer_reading'    => ['required', 'integer', 'min:0'],
            'odometer_photo'      => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
        ]);

        $data = $validated;
        $data['requested_by'] = auth()->id();
        $data['requested_at'] = now();
        $data['status']       = 'requested';

        // Store odometer photo (admin can also request fuel)
        if ($request->hasFile('odometer_photo')) {
            $data['odometer_photo_path'] = $this->storePhotoWithOriginalName(
                $request->file('odometer_photo'),
                'odometer_photos'
            );
        }

        FuelRequest::create($data);

        return redirect()->route('admin.fuel-requests.index')
            ->with('success', 'Fuel request created successfully.');
    }

    public function show(FuelRequest $fuelRequest)
    {
        $fuelRequest->load(['requester', 'vehicle', 'approvedBy', 'paymentRequest']);

        return view('admin.fuel-requests.show', compact('fuelRequest'));
    }

    /**
     * Approve a fuel request (set approved litres)
     */
    public function approve(Request $request, FuelRequest $fuelRequest)
    {
        // Prevent self-approval
        if ($fuelRequest->requested_by === auth()->id()) {
            return back()->with('error', 'You cannot approve your own fuel request.');
        }

        // Only allow approval on requested or pending status
        if (!in_array($fuelRequest->status, ['requested', 'pending'])) {
            return back()->with('error', 'This request cannot be approved in its current status.');
        }

        $validated = $request->validate([
            'actual_litres_dispensed' => ['required', 'numeric', 'min:0.01', 'max:' . ($fuelRequest->requested_amount * 1.5)],
            'admin_notes'             => ['nullable', 'string', 'max:1000'],
        ]);

        $fuelRequest->update([
            'status'                  => 'approved',
            'approved_by'             => auth()->id(),
            'approved_at'             => now(),
            'actual_litres_dispensed' => $validated['actual_litres_dispensed'],
            'admin_notes'             => $validated['admin_notes'] ?? $fuelRequest->admin_notes,
        ]);

        return redirect()->route('admin.fuel-requests.index')
            ->with('success', "Fuel request approved for {$validated['actual_litres_dispensed']} litres.");
    }

    /**
     * Reject a fuel request
     */
    public function reject(Request $request, FuelRequest $fuelRequest)
    {
        if ($fuelRequest->requested_by === auth()->id()) {
            return back()->with('error', 'You cannot reject your own fuel request.');
        }

        if (!in_array($fuelRequest->status, ['requested', 'pending'])) {
            return back()->with('error', 'This request cannot be rejected in its current status.');
        }

        $validated = $request->validate([
            'admin_notes' => ['required', 'string', 'max:1000'],
        ]);

        $fuelRequest->update([
            'status'      => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'admin_notes' => $validated['admin_notes'],
        ]);

        return redirect()->route('admin.fuel-requests.index')
            ->with('success', 'Fuel request rejected successfully.');
    }

    /**
     * Same photo storage helper as in driver controller
     */
    private function storePhotoWithOriginalName($file, string $folder): string
    {
        $originalName = $file->getClientOriginalName();
        $extension    = $file->getClientOriginalExtension();
        $baseName     = pathinfo($originalName, PATHINFO_FILENAME);
        $safeBase     = Str::slug($baseName);

        $filename = $safeBase . '.' . $extension;
        $path     = "{$folder}/{$filename}";
        $counter  = 1;

        while (Storage::disk('public')->exists($path)) {
            $filename = "{$safeBase}-({$counter}).{$extension}";
            $path     = "{$folder}/{$filename}";
            $counter++;
        }

        $file->storeAs($folder, $filename, 'public');

        return $path;
    }



public function approvePayment(FuelRequest $fuelRequest)
{
    // Make sure only admin can approve
    if(auth()->id() === $fuelRequest->requested_by){
        return back()->with('error', 'You cannot approve your own payment.');
    }

    // Update payment request status
    $fuelRequest->paymentRequest->update([
        'status' => 'approved',
    ]);

    // Optionally update the fuel request status
    $fuelRequest->update([
        'status' => 'approved', // or 'completed' if payment approval finalizes it
    ]);

    return back()->with('success', 'Payment approved successfully.');
}

public function rejectPayment(Request $request, FuelRequest $fuelRequest)
{
    if(auth()->id() === $fuelRequest->requested_by){
        return back()->with('error', 'You cannot reject your own payment.');
    }

    $request->validate([
        'notes' => 'required|string|max:1000',
    ]);

    $fuelRequest->paymentRequest->update([
        'status' => 'rejected',
        'notes' => $request->notes, // save reason for rejection
    ]);

    // Optionally update fuel request status
    $fuelRequest->update([
        'status' => 'payment_rejected',
    ]);

    return back()->with('success', 'Payment rejected successfully.');
}

}
