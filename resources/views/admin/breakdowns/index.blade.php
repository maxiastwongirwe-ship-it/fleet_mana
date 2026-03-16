@extends('layouts.admin')

@section('title', 'Breakdowns')

@section('content')

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
        <h1 class="text-3xl font-semibold text-gray-900 tracking-tight">Breakdowns</h1>

        <a href="{{ route('admin.breakdowns.create') }}"
           class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-indigo-700 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Breakdown
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($breakdowns->isEmpty())
            <div class="py-16 text-center text-gray-500">
                <p class="text-lg">No breakdowns recorded yet.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vehicle</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Driver</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Occurred</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Severity</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Approval</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($breakdowns as $breakdown)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $breakdown->vehicle->plate_number }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $breakdown->vehicle->make ?? '' }} {{ $breakdown->vehicle->model ?? '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $breakdown->driver?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $breakdown->occurred_at->format('d M Y • H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full {{ 
                                        match($breakdown->severity) {
                                            'critical' => 'bg-red-100 text-red-700',
                                            'major'    => 'bg-orange-100 text-orange-700',
                                            'moderate' => 'bg-yellow-100 text-yellow-700',
                                            'minor'    => 'bg-green-100 text-green-700',
                                            default    => 'bg-gray-100 text-gray-700',
                                        }
                                    }}">
                                        {{ ucfirst($breakdown->severity) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full {{ $breakdown->status_class }}">
                                        {{ ucfirst(str_replace('_', ' ', $breakdown->status)) }}
                                    </span>
                                </td>

                                <!-- Approval column – now shows real approver name -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($breakdown->approved)
                                        <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            Approved
                                        </span>
                                        <div class="text-xs text-gray-500 mt-1">
                                            by {{ $breakdown->approvedBy?->name ?? 'Unknown Admin' }}
                                        </div>
                                    @else
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>

                                            @auth
                                                @if(auth()->user()->isAdmin())
                                                    <form action="{{ route('admin.breakdowns.approve', $breakdown) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-xs text-green-600 hover:text-green-800 font-medium underline">
                                                            Approve
                                                        </button>
                                                    </form>
                                                @endif
                                            @endauth
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.breakdowns.show', $breakdown) }}" class="text-indigo-600 hover:text-indigo-800 mr-4">View</a>
                                    <a href="{{ route('admin.breakdowns.edit', $breakdown) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-5 border-t border-gray-100">
                {{ $breakdowns->links() }}
            </div>
        @endif
    </div>
</div>

@endsection