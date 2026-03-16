@extends('layouts.admin')

@section('title', 'Fuel Requests')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Fuel Requests</h1>
            <p class="mt-2 text-gray-600">Manage all driver and admin fuel requests</p>
        </div>
        <a href="{{ route('admin.fuel-requests.create') }}"
           class="inline-flex items-center px-5 py-3 bg-indigo-600 text-white font-medium rounded-lg shadow hover:bg-indigo-700 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create Request (Admin)
        </a>
    </div>

    <!-- Quick status filter -->
    <div class="mb-6 flex flex-wrap gap-3">
        <a href="{{ route('admin.fuel-requests.index') }}"
           class="px-4 py-2 rounded-lg {{ request()->missing('status') ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700' }} hover:bg-indigo-700 hover:text-white transition">
            All
        </a>
        <a href="{{ route('admin.fuel-requests.index', ['status' => 'requested']) }}"
           class="px-4 py-2 rounded-lg {{ request('status') === 'requested' ? 'bg-yellow-500 text-white' : 'bg-gray-200 text-gray-700' }} hover:bg-yellow-600 hover:text-white transition">
            Requested
        </a>
        <a href="{{ route('admin.fuel-requests.index', ['status' => 'approved']) }}"
           class="px-4 py-2 rounded-lg {{ request('status') === 'approved' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700' }} hover:bg-green-700 hover:text-white transition">
            Approved
        </a>
        <a href="{{ route('admin.fuel-requests.index', ['status' => 'completed']) }}"
           class="px-4 py-2 rounded-lg {{ request('status') === 'completed' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} hover:bg-blue-700 hover:text-white transition">
            Completed
        </a>
        <a href="{{ route('admin.fuel-requests.index', ['status' => 'rejected']) }}"
           class="px-4 py-2 rounded-lg {{ request('status') === 'rejected' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700' }} hover:bg-red-700 hover:text-white transition">
            Rejected
        </a>
        <a href="{{ route('admin.fuel-requests.index', ['status' => 'payment_pending']) }}"
           class="px-4 py-2 rounded-lg {{ request('status') === 'payment_pending' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700' }} hover:bg-purple-700 hover:text-white transition">
            Payment Pending
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Requester</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Vehicle</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Requested (L)</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Approved (L)</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($requests as $request)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $request->requester->name ?? 'Unknown' }}</div>
                                <div class="text-sm text-gray-500">{{ $request->requester->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-gray-900 font-medium">
                                {{ $request->vehicle->plate_number ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-gray-700">
                                {{ number_format($request->requested_amount, 2) }} L
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-gray-700">
                                {{ $request->actual_litres_dispensed ? number_format($request->actual_litres_dispensed, 2) . ' L' : '—' }}
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $request->status === 'approved' ? 'bg-green-100 text-green-800' : ($request->status === 'completed' ? 'bg-blue-100 text-blue-800' : ($request->status === 'payment_pending' ? 'bg-purple-100 text-purple-800' : ($request->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'))) }}">
                                    {{ $request->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.fuel-requests.show', $request) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                                No fuel requests found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-5 border-t border-gray-200">
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection