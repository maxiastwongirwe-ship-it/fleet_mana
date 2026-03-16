@extends('layouts.admin')

@section('title', 'Payment Request #' . $paymentRequest->id)

@section('content')
<div class="max-w-5xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-10">
        <h1 class="text-3xl font-semibold text-gray-900 tracking-tight">Payment Request #{{ $paymentRequest->id }}</h1>
        <a href="{{ route('admin.payment-requests.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to List
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 space-y-12">
        <!-- Payment Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div>
                <h3 class="text-lg font-medium text-gray-700">Driver</h3>
                <p class="text-xl font-medium mt-2">{{ $paymentRequest->requester->name }}</p>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-700">Vehicle</h3>
                <p class="text-xl font-medium mt-2">{{ $paymentRequest->fuelRequest->vehicle->plate_number }}</p>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-700">Amount</h3>
                <p class="text-3xl font-bold text-indigo-600 mt-2">UGX {{ number_format($paymentRequest->amount, 0) }}</p>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-700">Status</h3>
                <span class="px-5 py-2 inline-flex text-base font-medium rounded-full {{ 
                    match($paymentRequest->status) {
                        'pending'  => 'bg-purple-100 text-purple-800',
                        'approved' => 'bg-green-100 text-green-800',
                        'rejected' => 'bg-red-100 text-red-800',
                        default    => 'bg-gray-100 text-gray-800',
                    }
                }}">
                    {{ ucfirst($paymentRequest->status) }}
                </span>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-700">Requested At</h3>
                <p class="text-xl mt-2">{{ $paymentRequest->created_at->format('d M Y • H:i') }}</p>
            </div>
        </div>

        <!-- Fuel Request Summary -->
        <div class="border-t border-gray-200 pt-10">
            <h3 class="text-2xl font-semibold text-gray-900 mb-6">Related Fuel Request Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <p class="text-lg"><strong>Requested Litres:</strong> {{ number_format($paymentRequest->fuelRequest->requested_amount, 2) }} L</p>
                </div>
                <div>
                    <p class="text-lg"><strong>Actual Litres:</strong> 
                        {{ $paymentRequest->fuelRequest->actual_litres_dispensed ? number_format($paymentRequest->fuelRequest->actual_litres_dispensed, 2) . ' L' : 'Not filled yet' }}
                    </p>
                </div>
                <div>
                    <p class="text-lg"><strong>Station:</strong> {{ $paymentRequest->fuelRequest->station_name ?? 'Not specified' }}</p>
                </div>
                <div>
                    <p class="text-lg"><strong>Total Cost:</strong> UGX {{ number_format($paymentRequest->fuelRequest->total_cost ?? 0, 0) }}</p>
                </div>
                <div>
                    <p class="text-lg"><strong>Fuel Type:</strong> {{ $paymentRequest->fuelRequest->fuel_type }}</p>
                </div>
                <div>
                    <p class="text-lg"><strong>Requested By:</strong> {{ $paymentRequest->fuelRequest->requester->name }}</p>
                </div>
            </div>
        </div>

        <!-- Approve / Reject Buttons -->
        @if ($paymentRequest->isPending())
            <div class="border-t border-gray-200 pt-10 flex justify-end gap-6">
                <form action="{{ route('admin.payment-requests.approve', $paymentRequest) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="px-12 py-5 bg-green-600 text-white rounded-xl hover:bg-green-700 transition font-medium shadow-md">
                        Approve Payment
                    </button>
                </form>

                <form action="{{ route('admin.payment-requests.reject', $paymentRequest) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="px-12 py-5 bg-red-600 text-white rounded-xl hover:bg-red-700 transition font-medium shadow-md"
                            onclick="return confirm('Reject this payment request? You can add a reason on the next screen.')">
                        Reject Payment
                    </button>
                </form>
            </div>
        @elseif ($paymentRequest->isApproved())
            <div class="border-t border-gray-200 pt-10 text-center">
                <span class="px-10 py-5 inline-flex text-xl font-medium rounded-full bg-green-100 text-green-800 shadow-sm">
                    Payment Approved
                </span>
            </div>
        @elseif ($paymentRequest->isRejected())
            <div class="border-t border-gray-200 pt-10 text-center">
                <span class="px-10 py-5 inline-flex text-xl font-medium rounded-full bg-red-100 text-red-800 shadow-sm">
                    Payment Rejected
                </span>
                @if ($paymentRequest->notes)
                    <p class="mt-6 text-gray-700 text-lg bg-gray-50 p-6 rounded-xl">
                        Reason: {{ $paymentRequest->notes }}
                    </p>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection