@extends('layouts.admin')

@section('title', 'Transport Requests')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Transport Requests</h1>
                <p class="mt-2 text-gray-600">Manage passenger and goods transport requests</p>
            </div>
            <a href="{{ route('admin.transport-requests.create') }}"
               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-xl shadow hover:bg-indigo-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Request
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow p-6 mb-8">
            <form method="GET" class="flex flex-wrap gap-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="status"
                            class="block w-full sm:w-48 px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="grouped" {{ request('status') === 'grouped' ? 'selected' : '' }}>Grouped</option>
                        <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <div class="self-end">
                    <button type="submit"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow overflow-hidden border border-gray-100">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Requester</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pickup</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Drop-off</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pickup Time</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($requests as $request)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-5 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    {{ $request->request_type === 'passenger' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ ucfirst($request->request_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-gray-900 font-medium">
                                {{ $request->requester->name }}
                            </td>
                            <td class="px-6 py-5">
                                {{ $request->pickup_location }}
                            </td>
                            <td class="px-6 py-5">
                                {{ $request->dropoff_location }}
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-gray-600">
                                {{ $request->pickup_time->format('d M Y · H:i') }}
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    {{ match ($request->status) {
                                        'pending'   => 'bg-yellow-100 text-yellow-800',
                                        'approved'  => 'bg-blue-100 text-blue-800',
                                        'rejected'  => 'bg-red-100 text-red-800',
                                        'grouped'   => 'bg-purple-100 text-purple-800',
                                        'assigned'  => 'bg-indigo-100 text-indigo-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                    } }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.transport-requests.show', $request) }}"
                                   class="text-indigo-600 hover:text-indigo-900 mr-4">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-gray-500 text-lg">
                                No transport requests found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="px-6 py-5 border-t border-gray-200">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
@endsection