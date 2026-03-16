<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\FuelRequest;
use App\Models\PaymentRequest;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FuelRequestController extends Controller
{
    public function index()
    {
        $requests = FuelRequest::where('requested_by', auth()->id())
            ->with(['vehicle', 'approvedBy', 'paymentRequest'])
            ->orderByDesc('requested_at')
            ->paginate(15);

        return view('driver.fuel-requests.index', compact('requests'));
    }

    public function create()
    {
        $vehicles = Vehicle::orderBy('plate_number')->get();
        return view('driver.fuel-requests.create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id'       => ['required', 'exists:vehicles,id'],
            'requested_amount' => ['required', 'numeric', 'min:0.01'],
            'fuel_type'        => ['required', 'in:Petrol,Diesel'],
            'reason'           => ['nullable', 'string', 'max:1000'],
            'odometer_reading' => ['required', 'integer', 'min:0'],
            'odometer_photo'   => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
        ]);

        $data = $validated;
        $data['requested_by'] = auth()->id();
        $data['requested_at'] = now();
        $data['status']       = 'requested';

        // Store odometer photo in dedicated folder with original name + unique suffix
        if ($request->hasFile('odometer_photo')) {
            $data['odometer_photo_path'] = $this->storePhotoWithOriginalName(
                $request->file('odometer_photo'),
                'odometer_photos'
            );
        }

        FuelRequest::create($data);

        return redirect()->route('driver.fuel-requests.index')
            ->with('success', 'Fuel request submitted successfully. Awaiting admin approval.');
    }

    public function show(FuelRequest $fuelRequest)
    {
        if ($fuelRequest->requested_by !== auth()->id()) {
            abort(403);
        }

        $fuelRequest->load(['vehicle', 'approvedBy', 'paymentRequest']);

        return view('driver.fuel-requests.show', compact('fuelRequest'));
    }

    public function complete(FuelRequest $fuelRequest)
    {
        if ($fuelRequest->requested_by !== auth()->id()) {
            abort(403);
        }

        if (!$fuelRequest->isApproved()) {
            return redirect()->route('driver.fuel-requests.show', $fuelRequest)
                ->with('error', 'This request has not been approved yet.');
        }

        if ($fuelRequest->isCompleted() || $fuelRequest->paymentRequest) {
            return redirect()->route('driver.fuel-requests.show', $fuelRequest)
                ->with('info', 'This request is already completed or payment has been requested.');
        }

        return view('driver.fuel-requests.complete', compact('fuelRequest'));
    }

    public function storeCompletion(Request $request, FuelRequest $fuelRequest)
    {
        if ($fuelRequest->requested_by !== auth()->id() || !$fuelRequest->isApproved()) {
            abort(403);
        }

        $validated = $request->validate([
            'station_name'           => ['required', 'string', 'max:255'],
            'price_per_litre'        => ['required', 'numeric', 'min:0.01'],
            'actual_litres_dispensed'=> ['required', 'numeric', 'min:0.01'],
            'total_cost'             => ['required', 'numeric', 'min:0.01'],
            'payment_method'         => ['required', 'in:cash,mobile_money,bank_transfer,card'],
            'promocode'              => ['required_if:payment_method,mobile_money', 'nullable', 'string', 'max:100'],
            'bank_account'           => ['required_if:payment_method,bank_transfer', 'nullable', 'string', 'max:100'],
            'card_details'           => ['required_if:payment_method,card', 'nullable', 'string', 'max:100'],
            'receipt_photo'          => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'fillup_notes'           => ['nullable', 'string', 'max:1000'],
        ]);

        // Store receipt photo in dedicated folder with original name + unique suffix
        if ($request->hasFile('receipt_photo')) {
            if ($fuelRequest->receipt_photo_path) {
                Storage::disk('public')->delete($fuelRequest->receipt_photo_path);
            }
            $validated['receipt_photo_path'] = $this->storePhotoWithOriginalName(
                $request->file('receipt_photo'),
                'receipt_photos'
            );
        }

        $validated['status'] = 'completed';

        $fuelRequest->update($validated);

        return redirect()->route('driver.fuel-requests.show', $fuelRequest)
            ->with('success', 'Fuel fill-up completed successfully. You can now request payment.');
    }

    public function requestPayment(FuelRequest $fuelRequest)
    {
        if ($fuelRequest->requested_by !== auth()->id()) {
            abort(403);
        }

        if (!$fuelRequest->isCompleted()) {
            return back()->with('error', 'Fill-up must be completed before requesting payment.');
        }

        if ($fuelRequest->paymentRequest) {
            return back()->with('info', 'Payment has already been requested for this fill-up.');
        }

        PaymentRequest::create([
            'fuel_request_id' => $fuelRequest->id,
            'requested_by'    => auth()->id(),
            'amount'          => $fuelRequest->total_cost,
            'status'          => 'pending',
            'notes'           => 'Payment request for fuel fill-up - ' . now()->format('d M Y H:i'),
        ]);

        $fuelRequest->update(['status' => 'payment_pending']);

        return redirect()->route('driver.fuel-requests.show', $fuelRequest)
            ->with('success', 'Payment request submitted successfully. Awaiting admin approval.');
    }

    /**
     * Store photo with original filename (slugified) + unique counter if needed
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
