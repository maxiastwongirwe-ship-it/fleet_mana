@extends('layouts.admin')

@section('title', 'Fuel Request Details')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- HEADER -->
    <div class="flex justify-between items-start mb-8">

        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                Fuel Request #{{ $fuelRequest->id }}
            </h1>

            <p class="mt-2 text-gray-600">
                Requested by:
                <strong>{{ $fuelRequest->requester->name ?? 'Unknown' }}</strong>

                •

                Vehicle:
                <strong>{{ $fuelRequest->vehicle->plate_number ?? 'N/A' }}</strong>
            </p>
        </div>

        <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full

        {{ match($fuelRequest->status) {

            'approved' => 'bg-green-100 text-green-800',

            'completed' => 'bg-blue-100 text-blue-800',

            'payment_pending' => 'bg-purple-100 text-purple-800',

            'payment_approved' => 'bg-indigo-100 text-indigo-800',

            'payment_rejected' => 'bg-red-100 text-red-800',

            'rejected' => 'bg-red-100 text-red-800',

            default => 'bg-yellow-100 text-yellow-800'
        } }}">

            {{ $fuelRequest->status_label }}

        </span>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- LEFT COLUMN -->
        <div class="lg:col-span-2 space-y-8">

            <!-- REQUEST DETAILS -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6">

                <h2 class="text-xl font-semibold mb-4">
                    Request Details
                </h2>

                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <dt class="text-sm font-medium text-gray-500">
                            Requested Amount
                        </dt>

                        <dd class="mt-1 text-gray-900 font-medium">
                            {{ number_format($fuelRequest->requested_amount, 2) }} L
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">
                            Approved / Dispensed
                        </dt>

                        <dd class="mt-1 text-gray-900 font-medium">

                            {{ $fuelRequest->actual_litres_dispensed
                                ? number_format($fuelRequest->actual_litres_dispensed, 2).' L'
                                : 'Pending'
                            }}

                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">
                            Fuel Type
                        </dt>

                        <dd class="mt-1 text-gray-900">
                            {{ $fuelRequest->fuel_type }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">
                            Odometer at Request
                        </dt>

                        <dd class="mt-1 text-gray-900">
                            {{ number_format($fuelRequest->odometer_reading) }} km
                        </dd>
                    </div>

                    <div class="col-span-2">
                        <dt class="text-sm font-medium text-gray-500">
                            Reason / Notes
                        </dt>

                        <dd class="mt-1 text-gray-700">
                            {{ $fuelRequest->reason ?? 'No additional notes' }}
                        </dd>
                    </div>

                    <div class="col-span-2">
                        <dt class="text-sm font-medium text-gray-500">
                            Admin Notes
                        </dt>

                        <dd class="mt-1 text-gray-700">
                            {{ $fuelRequest->admin_notes ?? '—' }}
                        </dd>
                    </div>

                </dl>

            </div>

            <!-- FUEL THEFT PREDICTION -->
            @if($theftAnalysis)

            <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6">

                <div class="flex items-center justify-between mb-6">

                    <div>

                        <h2 class="text-2xl font-bold text-gray-900">
                            Fuel Theft Prediction
                        </h2>

                        <p class="text-gray-500 mt-1">
                            AI analysis using last 5 payment completed fuel records
                        </p>

                    </div>

                    <!-- STATUS BADGE -->
                    <div class="px-5 py-3 rounded-full text-sm font-bold

                    {{
                        $theftAnalysis['status'] === 'SUSPECTED FUEL THEFT'
                            ? 'bg-red-100 text-red-700'
                            : (
                                $theftAnalysis['status'] === 'INSUFFICIENT DATA'
                                    ? 'bg-yellow-100 text-yellow-700'
                                    : 'bg-green-100 text-green-700'
                            )
                    }}">

                        {{ $theftAnalysis['status'] }}

                    </div>

                </div>

                <!-- INSUFFICIENT DATA -->
                @if($theftAnalysis['status'] === 'INSUFFICIENT DATA')

                <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6">

                    <div class="flex items-start gap-4">

                        <div class="text-yellow-600 text-3xl">
                            ⚠️
                        </div>

                        <div>

                            <h3 class="text-lg font-semibold text-yellow-800">
                                Not Enough Historical Data
                            </h3>

                            <p class="mt-2 text-yellow-700">

                                This vehicle currently has only

                                <strong>
                                    {{ $theftAnalysis['available_logs'] ?? 0 }}
                                </strong>

                                completed fuel logs.

                            </p>

                            <p class="mt-2 text-yellow-700">

                                At least
                                <strong>5 payment completed records</strong>
                                are required before abnormal consumption analysis can begin.

                            </p>

                            <p class="mt-3 text-sm text-yellow-700">
                                Admin can still approve this fuel request normally.
                            </p>

                        </div>

                    </div>

                </div>

                @elseif($theftAnalysis['status'] === 'NO ANALYSIS')

                <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6">

                    <p class="text-gray-700 font-medium">
                        {{ $theftAnalysis['message'] }}
                    </p>

                </div>

                @else

                <!-- ANALYSIS GRID -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div class="bg-gray-50 rounded-2xl p-5">

                        <p class="text-sm text-gray-500">
                            Average Consumption
                        </p>

                        <h3 class="text-2xl font-bold mt-2">
                            {{ $theftAnalysis['average_consumption'] }}
                            L/KM
                        </h3>

                    </div>

                    <div class="bg-gray-50 rounded-2xl p-5">

                        <p class="text-sm text-gray-500">
                            Distance Since Last Refuel
                        </p>

                        <h3 class="text-2xl font-bold mt-2">
                            {{ number_format($theftAnalysis['distance_travelled'], 2) }}
                            KM
                        </h3>

                    </div>

                    <div class="bg-gray-50 rounded-2xl p-5">

                        <p class="text-sm text-gray-500">
                            Expected Fuel Usage
                        </p>

                        <h3 class="text-2xl font-bold mt-2">
                            {{ number_format($theftAnalysis['expected_fuel'], 2) }}
                            L
                        </h3>

                    </div>

                    <div class="bg-gray-50 rounded-2xl p-5">

                        <p class="text-sm text-gray-500">
                            Requested Fuel
                        </p>

                        <h3 class="text-2xl font-bold mt-2">
                            {{ number_format($theftAnalysis['requested_fuel'], 2) }}
                            L
                        </h3>

                    </div>

                    <div class="bg-gray-50 rounded-2xl p-5 md:col-span-2">

                        <p class="text-sm text-gray-500">
                            Difference
                        </p>

                        <h3 class="text-3xl font-bold mt-2

                        {{ $theftAnalysis['difference'] > 10
                            ? 'text-red-600'
                            : 'text-green-600'
                        }}">

                            {{ number_format($theftAnalysis['difference'], 2) }}

                            Litres

                        </h3>

                    </div>

                </div>

                <!-- RESULT MESSAGE -->
                <div class="mt-6 rounded-2xl p-5

                {{ $theftAnalysis['status'] === 'SUSPECTED FUEL THEFT'
                    ? 'bg-red-50 border border-red-200'
                    : 'bg-green-50 border border-green-200'
                }}">

                    <p class="font-semibold text-lg

                    {{ $theftAnalysis['status'] === 'SUSPECTED FUEL THEFT'
                        ? 'text-red-700'
                        : 'text-green-700'
                    }}">

                        {{ $theftAnalysis['message'] }}

                    </p>

                </div>

                @endif

            </div>

            @endif

            <!-- ODOMETER PHOTO -->
            @if ($fuelRequest->odometer_photo_url)

            <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6">

                <h3 class="text-lg font-semibold mb-4">
                    Odometer Photo (Request Time)
                </h3>

                <img
                    src="{{ $fuelRequest->odometer_photo_url }}"
                    class="max-h-80 rounded-lg shadow-sm object-contain"
                >

            </div>

            @endif

            <!-- RECEIPT PHOTO -->
            @if ($fuelRequest->isCompleted() && $fuelRequest->receipt_photo_url)

            <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6">

                <h3 class="text-lg font-semibold mb-4">
                    Receipt Photo
                </h3>

                <img
                    src="{{ $fuelRequest->receipt_photo_url }}"
                    class="max-h-96 rounded-lg shadow-sm object-contain"
                >

            </div>

            @endif

        </div>

        <!-- RIGHT COLUMN -->
        <div class="space-y-6">

            <!-- CURRENT STATUS -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6">

                <h2 class="text-xl font-semibold mb-4">
                    Current Status
                </h2>

                <p class="text-lg font-bold

                {{ match($fuelRequest->status) {

                    'approved' => 'text-green-600',

                    'completed' => 'text-blue-600',

                    'payment_pending' => 'text-purple-600',

                    'payment_approved' => 'text-indigo-600',

                    'payment_rejected' => 'text-red-600',

                    'rejected' => 'text-red-600',

                    default => 'text-yellow-600'
                } }}">

                    {{ $fuelRequest->status_label }}

                </p>

                @if ($fuelRequest->paymentRequest)

                <div class="mt-4 pt-4 border-t border-gray-200">

                    <h3 class="text-lg font-semibold mb-2">
                        Payment Request
                    </h3>

                    <p>
                        Amount:
                        <strong>
                            {{ number_format($fuelRequest->paymentRequest->amount, 2) }}
                        </strong>
                    </p>

                    <p class="mt-1">

                        Status:

                        <strong class="{{ match($fuelRequest->paymentRequest->status) {

                            'approved' => 'text-green-600',

                            'rejected' => 'text-red-600',

                            default => 'text-purple-600'
                        } }}">

                            {{ $fuelRequest->paymentRequest->status_label }}

                        </strong>

                    </p>

                </div>

                @endif

            </div>

            <!-- APPROVE / REJECT -->
            @if (in_array($fuelRequest->status,['requested','pending']))

            <!-- APPROVE -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6">

                <h3 class="text-lg font-semibold mb-4 text-green-700">
                    Approve Request
                </h3>

                @if (auth()->id() === $fuelRequest->requested_by)

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center text-yellow-800 mb-4">
                    You cannot approve your own request
                </div>

                @endif

                <form
                    action="{{ route('admin.fuel-requests.approve',$fuelRequest) }}"
                    method="POST"
                >

                    @csrf

                    <div class="mb-4">

                        <label class="block text-sm font-medium text-gray-700">
                            Approved Litres
                        </label>

                        <input
                            type="number"
                            name="actual_litres_dispensed"
                            step="0.01"
                            min="0.01"
                            max="{{ $fuelRequest->requested_amount * 1.5 }}"
                            value="{{ old('actual_litres_dispensed',$fuelRequest->requested_amount) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"

                            @if(auth()->id() === $fuelRequest->requested_by)
                                disabled
                            @endif

                            required
                        >

                    </div>

                    <div class="mb-4">

                        <label class="block text-sm font-medium text-gray-700">
                            Admin Notes
                        </label>

                        <textarea
                            name="admin_notes"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"

                            @if(auth()->id() === $fuelRequest->requested_by)
                                disabled
                            @endif
                        ></textarea>

                    </div>

                    <button
                        type="submit"
                        class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700"

                        @if(auth()->id() === $fuelRequest->requested_by)
                            disabled
                        @endif
                    >
                        Approve Request
                    </button>

                </form>

            </div>

            <!-- REJECT -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6">

                <h3 class="text-lg font-semibold mb-4 text-red-700">
                    Reject Request
                </h3>

                @if (auth()->id() === $fuelRequest->requested_by)

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center text-yellow-800 mb-4">
                    You cannot reject your own request
                </div>

                @endif

                <form
                    action="{{ route('admin.fuel-requests.reject',$fuelRequest) }}"
                    method="POST"
                >

                    @csrf

                    <textarea
                        name="admin_notes"
                        rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"

                        @if(auth()->id() === $fuelRequest->requested_by)
                            disabled
                        @endif

                        required
                    ></textarea>

                    <button
                        type="submit"
                        class="mt-4 w-full bg-red-600 text-white py-3 rounded-lg hover:bg-red-700"

                        @if(auth()->id() === $fuelRequest->requested_by)
                            disabled
                        @endif
                    >
                        Reject Request
                    </button>

                </form>

            </div>

            @endif

            <!-- PAYMENT ACTIONS -->
            @if ($fuelRequest->status === 'completed' || $fuelRequest->status === 'payment_pending')

            <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6">

                <h3 class="text-lg font-semibold mb-4 text-purple-700">
                    Payment Actions
                </h3>

                @if (auth()->id() === $fuelRequest->requested_by)

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center text-yellow-800 mb-4">
                    You cannot approve your own payment
                </div>

                @endif

                <!-- APPROVE PAYMENT -->
                <form
                    action="{{ route('admin.fuel-requests.approvePayment', $fuelRequest) }}"
                    method="POST"
                    class="mb-4"
                >

                    @csrf

                    <button
                        type="submit"
                        class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700"

                        @if(auth()->id() === $fuelRequest->requested_by)
                            disabled
                        @endif
                    >
                        Approve Payment
                    </button>

                </form>

                <!-- REJECT PAYMENT -->
                <form
                    action="{{ route('admin.fuel-requests.rejectPayment', $fuelRequest) }}"
                    method="POST"
                >

                    @csrf

                    <textarea
                        name="notes"
                        rows="3"
                        placeholder="Reason for rejecting payment"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm mb-2"

                        @if(auth()->id() === $fuelRequest->requested_by)
                            disabled
                        @endif

                        required
                    ></textarea>

                    <button
                        type="submit"
                        class="w-full bg-red-600 text-white py-3 rounded-lg hover:bg-red-700"

                        @if(auth()->id() === $fuelRequest->requested_by)
                            disabled
                        @endif
                    >
                        Reject Payment
                    </button>

                </form>

            </div>

            @endif

        </div>

    </div>

    <div class="mt-8 text-center">

        <a
            href="{{ route('admin.fuel-requests.index') }}"
            class="text-indigo-600 hover:text-indigo-800 font-medium"
        >
            ← Back to All Requests
        </a>

    </div>

</div>
@endsection