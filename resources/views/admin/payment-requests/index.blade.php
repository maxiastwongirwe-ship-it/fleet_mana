@extends('layouts.admin')

@section('title', 'Payment Requests')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-semibold text-gray-900 tracking-tight mb-10">Payment Requests</h1>

    @if($payments->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center text-gray-500 text-lg">
            No payment requests pending.
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                            <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Driver</th>
                            <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Vehicle</th>
                            <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Amount</th>
                            <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-8 py-5 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($payments as $payment)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-8 py-6 whitespace-nowrap font-medium">
                                    #{{ $payment->id }}
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    {{ $payment->requester->name }}
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    {{ $payment->fuelRequest->vehicle->plate_number }}
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap font-medium text-gray-900">
                                    UGX {{ number_format($payment->amount, 0) }}
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <span class="px-4 py-1.5 inline-flex text-sm font-medium rounded-full {{ 
                                        match($payment->status) {
                                            'pending' => 'bg-purple-100 text-purple-800',
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        }
                                    }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.payment-requests.show', $payment) }}" class="text-indigo-600 hover:text-indigo-800">
                                        View & Approve
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-8 py-6 border-t border-gray-100">
                {{ $payments->links() }}
            </div>
        </div>
    @endif
</div>
@endsection