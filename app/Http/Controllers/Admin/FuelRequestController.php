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
    $fuelRequest->load([
        'requester',
        'vehicle',
        'approvedBy',
        'paymentRequest'
    ]);

    $vehicle = $fuelRequest->vehicle;

    $theftAnalysis = null;

    if ($vehicle) {

        /*
        |--------------------------------------------------------------------------
        | GET LAST 5 PAYMENT APPROVED REQUESTS
        |--------------------------------------------------------------------------
        */

        $previousRequests = FuelRequest::where('vehicle_id', $vehicle->id)
            ->where('status', 'payment_approved')
            ->where('id', '!=', $fuelRequest->id)
            ->orderByDesc('id')
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | IF LESS THAN 5 RECORDS
        |--------------------------------------------------------------------------
        */

        if ($previousRequests->count() < 5) {

            $theftAnalysis = [
                'status' => 'INSUFFICIENT DATA',
                'available_logs' => $previousRequests->count(),
            ];

        } else {

            /*
            |--------------------------------------------------------------------------
            | CALCULATE AVERAGE CONSUMPTION
            |--------------------------------------------------------------------------
            */

            $consumptions = [];

            foreach ($previousRequests as $index => $request) {

                $nextRequest = $previousRequests[$index - 1] ?? null;

                if (!$nextRequest) {
                    continue;
                }

                $distance = $nextRequest->odometer_reading - $request->odometer_reading;

                if ($distance <= 0) {
                    continue;
                }

                $litres = $nextRequest->actual_litres_dispensed;

                $consumptionPerKm = $litres / $distance;

                $consumptions[] = $consumptionPerKm;
            }

            /*
            |--------------------------------------------------------------------------
            | CHECK VALID CONSUMPTIONS
            |--------------------------------------------------------------------------
            */

            if (count($consumptions) == 0) {

                $theftAnalysis = [
                    'status' => 'NO ANALYSIS',
                    'message' => 'Unable to calculate fuel consumption history.',
                ];

            } else {

                $averageConsumption = array_sum($consumptions) / count($consumptions);

                /*
                |--------------------------------------------------------------------------
                | GET MOST RECENT PREVIOUS REQUEST
                |--------------------------------------------------------------------------
                */

                $lastRequest = FuelRequest::where('vehicle_id', $vehicle->id)
                    ->where('status', 'payment_approved')
                    ->where('id', '!=', $fuelRequest->id)
                    ->latest('id')
                    ->first();

                if ($lastRequest) {

                    $distanceTravelled =
                        $fuelRequest->odometer_reading -
                        $lastRequest->odometer_reading;

                    if ($distanceTravelled < 0) {
                        $distanceTravelled = 0;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | EXPECTED FUEL
                    |--------------------------------------------------------------------------
                    */

                    $expectedFuel =
                        $averageConsumption * $distanceTravelled;

                    $requestedFuel =
                        $fuelRequest->requested_amount;

                    $difference =
                        $requestedFuel - $expectedFuel;

                    /*
                    |--------------------------------------------------------------------------
                    | DETECT ANOMALY
                    |--------------------------------------------------------------------------
                    */

                    $status =
                        $difference > 10
                            ? 'SUSPECTED FUEL THEFT'
                            : 'NO SUSPICION FOUND';

                    $message =
                        $difference > 10
                            ? 'Requested fuel exceeds expected vehicle consumption by more than 10 litres.'
                            : 'Fuel request falls within normal vehicle consumption behaviour.';

                    $theftAnalysis = [

                        'status' => $status,

                        'average_consumption' => round($averageConsumption, 4),

                        'distance_travelled' => $distanceTravelled,

                        'expected_fuel' => round($expectedFuel, 2),

                        'requested_fuel' => round($requestedFuel, 2),

                        'difference' => round($difference, 2),

                        'message' => $message,
                    ];
                }
            }
        }
    }

    return view(
        'admin.fuel-requests.show',
        compact('fuelRequest', 'theftAnalysis')
    );
}

    // ==================== APPROVE / REJECT ====================

    public function approve(Request $request, FuelRequest $fuelRequest)
    {
        if ($fuelRequest->requested_by === auth()->id()) {
            return back()->with('error', 'You cannot approve your own fuel request.');
        }

        if (!in_array($fuelRequest->status, ['requested', 'pending'])) {
            return back()->with('error', 'This request cannot be approved in its current status.');
        }

        $validated = $request->validate([
            'actual_litres_dispensed' => ['required', 'numeric', 'min:0.01'],
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

    // ==================== PAYMENT APPROVAL ====================

    public function approvePayment(FuelRequest $fuelRequest)
    {
        if (auth()->id() === $fuelRequest->requested_by) {
            return back()->with('error', 'You cannot approve your own payment.');
        }

        // Check if payment request exists
        if (!$fuelRequest->paymentRequest) {
            return back()->with('error', 'No payment request found for this fuel request.');
        }

        $fuelRequest->paymentRequest->update([
            'status' => 'approved',
        ]);

        // Update fuel request status
        $fuelRequest->update([
            'status' => 'payment_approved',
        ]);

        return back()->with('success', 'Payment approved successfully.');
    }

    public function rejectPayment(Request $request, FuelRequest $fuelRequest)
    {
        if (auth()->id() === $fuelRequest->requested_by) {
            return back()->with('error', 'You cannot reject your own payment.');
        }

        if (!$fuelRequest->paymentRequest) {
            return back()->with('error', 'No payment request found for this fuel request.');
        }

        $validated = $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $fuelRequest->paymentRequest->update([
            'status' => 'rejected',
            'notes'  => $validated['notes'],
        ]);

        $fuelRequest->update([
            'status' => 'payment_rejected',
        ]);

        return back()->with('success', 'Payment rejected successfully.');
    }

    /**
     * Photo storage helper
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
}
