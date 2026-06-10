@extends('layouts.worker')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-10">
    <!-- Welcome Header -->
    <div>
        <h1 class="text-4xl font-semibold tracking-tight text-gray-900">
            Good morning, {{ Auth::user()->name }} 👋
        </h1>
        <p class="text-gray-600 mt-2 text-lg">Here's an overview of your transport activity</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="apple-card p-8">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Requests</p>
                    <p class="text-5xl font-semibold mt-3 text-gray-900">{{ $totalRequests ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-3xl flex items-center justify-center text-3xl">
                    📋
                </div>
            </div>
        </div>

        <div class="apple-card p-8">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-5xl font-semibold mt-3 text-amber-600">{{ $pendingRequests ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-3xl flex items-center justify-center text-3xl">
                    ⏳
                </div>
            </div>
        </div>

        <div class="apple-card p-8">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Assigned</p>
                    <p class="text-5xl font-semibold mt-3 text-emerald-600">{{ $assignedRequests ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-3xl flex items-center justify-center text-3xl">
                    🚐
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Requests -->
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold tracking-tight">Recent Requests</h2>
            <a href="{{ route('worker.transport-requests.index') }}" 
               class="text-blue-600 hover:text-blue-700 font-medium flex items-center gap-2">
                View All →
            </a>
        </div>

        @if(isset($recentRequests) && $recentRequests->isNotEmpty())
            <div class="grid gap-6">
                @foreach($recentRequests as $request)
                    <a href="{{ route('worker.transport-requests.show', $request) }}" 
                       class="apple-card p-7 hover:shadow-xl transition-all block">
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

                                <p class="mt-4 font-medium text-lg">
                                    {{ $request->pickup_location }} → {{ $request->dropoff_location }}
                                </p>
                            </div>

                            <div class="text-right">
                                <span class="inline-block px-6 py-2 rounded-3xl text-sm font-semibold
                                    @if($request->isPending()) bg-yellow-100 text-yellow-700
                                    @elseif($request->isAssigned() || $request->isApproved()) bg-emerald-100 text-emerald-700
                                    @elseif($request->isRejected()) bg-red-100 text-red-700
                                    @endif">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="apple-card p-16 text-center">
                <p class="text-gray-400 text-xl">No requests yet.</p>
                <a href="{{ route('worker.transport-requests.create') }}" 
                   class="mt-8 inline-block bg-black text-white px-8 py-4 rounded-3xl font-medium">
                    Create Your First Request
                </a>
            </div>
        @endif
    </div>

    <!-- Quick Action -->
    <div class="apple-card p-10 text-center">
        <h3 class="text-2xl font-semibold">Ready to book a vehicle?</h3>
        <p class="text-gray-600 mt-3 max-w-md mx-auto">
            Request transport for field work or goods delivery
        </p>
        <a href="{{ route('worker.transport-requests.create') }}" 
           class="mt-8 inline-block bg-black hover:bg-gray-900 text-white px-10 py-5 rounded-3xl text-lg font-semibold transition">
            New Transport Request →
        </a>
    </div>
</div>
@endsection