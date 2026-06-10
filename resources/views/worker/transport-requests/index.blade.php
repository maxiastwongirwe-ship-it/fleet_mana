@extends('layouts.worker')

@section('title', 'My Requests')

@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-semibold tracking-tight">My Transport Requests</h1>
            <p class="text-gray-600 mt-1">Track all your booking requests</p>
        </div>
        <a href="{{ route('worker.transport-requests.create') }}" 
           class="bg-black text-white px-8 py-4 rounded-3xl font-semibold flex items-center gap-3 hover:bg-gray-900 transition active:scale-95">
            <span class="text-2xl leading-none">+</span>
            <span>New Request</span>
        </a>
    </div>

    <div class="grid gap-6">
        @forelse($requests as $request)
            <div class="apple-card p-7 hover:shadow-xl transition-all block group">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex-1">
                        <div class="flex items-center gap-4">
                            <span class="inline-flex items-center px-4 py-1.5 rounded-3xl text-sm font-medium
                                {{ $request->request_type === 'passenger' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ ucfirst($request->request_type) }}
                            </span>
                            <span class="text-sm text-gray-500">
                                {{ $request->pickup_time->format('D, j M • g:i A') }}
                            </span>
                        </div>

                        <div class="mt-4">
                            <p class="font-medium text-lg leading-tight">
                                {{ $request->pickup_location }} <span class="text-gray-400">→</span> {{ $request->dropoff_location }}
                            </p>
                        </div>

                        @if($request->purpose)
                            <p class="mt-3 text-sm text-gray-600 line-clamp-2">{{ $request->purpose }}</p>
                        @endif
                    </div>

                    <div class="text-right md:text-left md:min-w-[180px] flex flex-col items-end gap-3">
                        <span class="inline-block px-6 py-2 rounded-3xl text-sm font-semibold
                            @if($request->isPending()) bg-yellow-100 text-yellow-700
                            @elseif($request->isApproved()) bg-blue-100 text-blue-700
                            @elseif($request->isAssigned()) bg-emerald-100 text-emerald-700
                            @elseif($request->isRejected()) bg-red-100 text-red-700
                            @elseif($request->isCompleted()) bg-gray-100 text-gray-700
                            @endif">
                            {{ ucfirst($request->status) }}
                        </span>

                        @if($request->trips->isNotEmpty())
                            <div class="flex gap-3">
                                <a href="{{ route('worker.transport-requests.show', $request) }}" 
                                   class="text-sm px-5 py-2 bg-gray-100 hover:bg-gray-200 rounded-2xl transition">
                                    View Details
                                </a>
                                
                                <a href="{{ route('worker.transport-requests.vehicle', $request) }}" 
                                   class="text-sm px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl transition flex items-center gap-2">
                                    <span>🚐</span>
                                    View Assigned Vehicle
                                </a>
                            </div>
                        @else
                            <a href="{{ route('worker.transport-requests.show', $request) }}" 
                               class="text-sm px-6 py-2 bg-indigo-600 text-white hover:bg-indigo-700 rounded-2xl transition">
                                View Details
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="apple-card p-16 text-center">
                <p class="text-gray-400 text-xl">You have no transport requests yet.</p>
                <a href="{{ route('worker.transport-requests.create') }}" 
                   class="mt-6 inline-block bg-black text-white px-8 py-4 rounded-3xl font-medium">
                    Make Your First Request
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $requests->links() }}
    </div>
</div>
@endsection