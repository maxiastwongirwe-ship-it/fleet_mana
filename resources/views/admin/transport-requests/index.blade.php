@extends('layouts.admin')

@section('title', 'Transport Requests')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Transport Requests</h1>
            <p class="mt-2 text-gray-600">Manage all passenger and goods transport requests</p>
        </div>
        <a href="{{ route('admin.transport-requests.create') }}"
           class="inline-flex items-center px-8 py-4 bg-indigo-600 text-white font-semibold rounded-3xl hover:bg-indigo-700 transition shadow-sm">
            + New Request
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-3xl shadow p-6 mb-8">
        <form method="GET" class="flex flex-wrap gap-6">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="status"
                        class="block w-full sm:w-52 px-5 py-3 border border-gray-300 rounded-2xl focus:ring-indigo-500">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            <div class="self-end">
                <button type="submit"
                        class="px-8 py-3 bg-indigo-600 text-white rounded-2xl hover:bg-indigo-700 transition">
                    Apply Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Requests Table -->
    <div class="bg-white rounded-3xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                    <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Requester</th>
                    <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Route</th>
                    <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pickup Time</th>
                    <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-8 py-5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($requests as $request)
                    <tr class="hover:bg-gray-50 transition-all">
                        <td class="px-8 py-6 whitespace-nowrap">
                            <span class="inline-flex px-4 py-2 rounded-2xl text-sm font-medium
                                {{ $request->request_type === 'passenger' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ ucfirst($request->request_type) }}
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="font-medium text-gray-900">{{ $request->requester->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="text-sm">
                                <span class="font-medium">{{ $request->pickup_location }}</span>
                                <span class="text-gray-400 mx-2">→</span>
                                <span class="font-medium">{{ $request->dropoff_location }}</span>
                            </div>
                            @if($request->pickup_lat && $request->dropoff_lat)
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ number_format($request->pickup_lat, 4) }}, {{ number_format($request->pickup_lng, 4) }}
                                    →
                                    {{ number_format($request->dropoff_lat, 4) }}, {{ number_format($request->dropoff_lng, 4) }}
                                </div>
                            @endif
                        </td>
                        <td class="px-8 py-6 whitespace-nowrap text-gray-600">
                            {{ $request->pickup_time->format('d M Y • H:i') }}
                        </td>
                        <td class="px-8 py-6 whitespace-nowrap">
                            <span class="inline-flex px-5 py-2 rounded-3xl text-sm font-semibold
                                @if($request->isPending()) bg-yellow-100 text-yellow-700
                                @elseif($request->isApproved()) bg-blue-100 text-blue-700
                                @elseif($request->isAssigned()) bg-emerald-100 text-emerald-700
                                @elseif($request->isRejected()) bg-red-100 text-red-700
                                @elseif($request->isCompleted()) bg-gray-100 text-gray-700
                                @endif">
                                {{ ucfirst($request->status) }}
                            </span>
                        </td>
                        <td class="px-8 py-6 whitespace-nowrap text-right">
                            <a href="{{ route('admin.transport-requests.show', $request) }}"
                               class="text-indigo-600 hover:text-indigo-800 font-medium mr-6">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center text-gray-500">
                            No transport requests found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-8 py-6 border-t">
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection