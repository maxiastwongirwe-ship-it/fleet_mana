@extends('layouts.app')

@section('title', 'Fuel Request Details')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col sm:flex-row justify-between items-start mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Fuel Request Details</h1>
            <p class="mt-2 text-gray-600">
                Vehicle: <strong>{{ $fuelRequest->vehicle->plate_number ?? 'N/A' }}</strong>
            </p>
        </div>

        <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full
            {{ match ($fuelRequest->status) {
                'approved'         => 'bg-green-100 text-green-800 border border-green-200',
                'completed'        => 'bg-blue-100 text-blue-800 border border-blue-200',
                'payment_pending'  => 'bg-purple-100 text-purple-800 border border-purple-200',
                'payment_approved' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                'rejected'         => 'bg-red-100 text-red-800 border border-red-200',
                'payment_rejected' => 'bg-rose-100 text-rose-800 border border-rose-200',
                default            => 'bg-yellow-100 text-yellow-800 border border-yellow-200'
            } }}">
            {{ $fuelRequest->status_label }}
        </span>
    </div>

    <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden divide-y divide-gray-200">

        {{-- Request Information --}}
        <div class="p-6">
            <h2 class="text-xl font-semibold mb-4">Request Information</h2>

            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Requested Amount</dt>
                    <dd class="mt-1 text-gray-900 font-medium">
                        {{ number_format($fuelRequest->requested_amount, 2) }} L
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Approved / Dispensed</dt>
                    <dd class="mt-1 text-gray-900 font-medium">
                        {{ $fuelRequest->actual_litres_dispensed ? number_format($fuelRequest->actual_litres_dispensed, 2).' L' : '—' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Fuel Type</dt>
                    <dd class="mt-1 text-gray-900">{{ $fuelRequest->fuel_type }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Odometer Reading</dt>
                    <dd class="mt-1 text-gray-900">
                        {{ number_format($fuelRequest->odometer_reading) }} km
                    </dd>
                </div>

                <div class="col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Reason</dt>
                    <dd class="mt-1 text-gray-700 whitespace-pre-wrap">
                        {{ $fuelRequest->reason ?? 'No reason provided' }}
                    </dd>
                </div>
            </dl>
        </div>


        {{-- Odometer Photo --}}
        @if ($fuelRequest->odometer_photo_url)
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-3">Odometer Photo</h3>

            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <img src="{{ $fuelRequest->odometer_photo_url }}"
                     alt="Odometer reading"
                     class="max-h-80 mx-auto rounded shadow object-contain">
            </div>
        </div>
        @endif


        {{-- Fill-up Details --}}
        @if ($fuelRequest->isCompleted() || $fuelRequest->receipt_photo_url)

        <div class="p-6">
            <h2 class="text-xl font-semibold mb-4">Fill-up Details</h2>

            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">

                <div>
                    <dt class="text-sm font-medium text-gray-500">Petrol Station</dt>
                    <dd class="mt-1 text-gray-900">
                        {{ $fuelRequest->station_name ?? '—' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Price per Litre</dt>
                    <dd class="mt-1 text-gray-900">
                        {{ $fuelRequest->price_per_litre ? number_format($fuelRequest->price_per_litre, 2) : '—' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Actual Litres Dispensed</dt>
                    <dd class="mt-1 text-gray-900 font-medium">
                        {{ $fuelRequest->actual_litres_dispensed ? number_format($fuelRequest->actual_litres_dispensed, 2).' L' : '—' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Total Cost</dt>
                    <dd class="mt-1 text-gray-900 font-medium">
                        {{ $fuelRequest->total_cost ? number_format($fuelRequest->total_cost, 2) : '—' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                    <dd class="mt-1 text-gray-900 capitalize">
                        {{ str_replace('_', ' ', $fuelRequest->payment_method ?? '—') }}
                    </dd>
                </div>

                @if ($fuelRequest->promocode || $fuelRequest->bank_account || $fuelRequest->card_details)
                <div class="col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Payment Reference</dt>
                    <dd class="mt-1 text-gray-900">
                        {{ $fuelRequest->promocode ?? $fuelRequest->bank_account ?? $fuelRequest->card_details ?? '—' }}
                    </dd>
                </div>
                @endif

                @if ($fuelRequest->fillup_notes)
                <div class="col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Notes</dt>
                    <dd class="mt-1 text-gray-700 whitespace-pre-wrap">
                        {{ $fuelRequest->fillup_notes }}
                    </dd>
                </div>
                @endif

            </dl>
        </div>


        {{-- Receipt Photo --}}
        @if ($fuelRequest->receipt_photo_url)
        <div class="p-6 border-t border-gray-200">

            <h3 class="text-lg font-semibold mb-3">Receipt Photo</h3>

            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <img src="{{ $fuelRequest->receipt_photo_url }}"
                     alt="Fuel receipt"
                     class="max-h-96 mx-auto rounded shadow object-contain">
            </div>

        </div>
        @endif

        @endif


        {{-- Payment Request --}}
        @if ($fuelRequest->paymentRequest)

        <div class="p-6 bg-gray-50">

            <h2 class="text-xl font-semibold mb-4">Payment Request</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <dt class="text-sm font-medium text-gray-500">Requested Amount</dt>
                    <dd class="mt-1 text-gray-900 font-medium">
                        {{ number_format($fuelRequest->paymentRequest->amount, 2) }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>

                    <dd class="mt-1">

                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                        {{ match($fuelRequest->paymentRequest->status) {
                            'approved' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                            default => 'bg-purple-100 text-purple-800'
                        } }}">

                            {{ $fuelRequest->paymentRequest->status_label }}

                        </span>

                    </dd>

                </div>

                @if ($fuelRequest->paymentRequest->notes)

                <div class="col-span-2">

                    <dt class="text-sm font-medium text-gray-500">Notes</dt>

                    <dd class="mt-1 text-gray-700">
                        {{ $fuelRequest->paymentRequest->notes }}
                    </dd>

                </div>

                @endif

            </div>

        </div>

        @endif

    </div>


    <div class="mt-8 flex justify-center">

        <a href="{{ route('driver.fuel-requests.index') }}"
           class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition">

            ← Back to My Requests

        </a>

    </div>

</div>
@endsection