@extends('layouts.driver')

@section('title', 'Driver Dashboard')

@section('content')
<div class="space-y-10">

    <!-- Welcome Header -->
    <div class="bg-white rounded-3xl shadow p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-4xl font-semibold tracking-tight text-gray-900">
                HELLO, {{ auth()->user()->name }} 👋
            </h1>
            <p class="mt-2 text-lg text-gray-600">Here's an overview of your current assignments</p>
        </div>
        <div class="text-right text-sm text-gray-500">
            {{ now()->format('d M Y') }}
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="apple-card p-8">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Upcoming Trips</p>
                    <p class="text-5xl font-semibold mt-4 text-amber-600">{{ $upcomingTrips ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 bg-amber-100 rounded-3xl flex items-center justify-center text-4xl">⏳</div>
            </div>
        </div>

        <div class="apple-card p-8">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Active Trips</p>
                    <p class="text-5xl font-semibold mt-4 text-emerald-600">{{ $activeTrips ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 bg-emerald-100 rounded-3xl flex items-center justify-center text-4xl">🚐</div>
            </div>
        </div>

        <div class="apple-card p-8">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Completed Today</p>
                    <p class="text-5xl font-semibold mt-4 text-sky-600">{{ $completedToday ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 bg-sky-100 rounded-3xl flex items-center justify-center text-4xl">✅</div>
            </div>
        </div>

        <div class="apple-card p-8">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Trips</p>
                    <p class="text-5xl font-semibold mt-4 text-gray-700">{{ $totalTrips ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 bg-gray-100 rounded-3xl flex items-center justify-center text-4xl">📊</div>
            </div>
        </div>
    </div>

    <!-- My Assigned Trips -->
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold tracking-tight">My Assigned Trips</h2>
            <a href="{{ route('driver.trips.index') }}" 
               class="text-sky-600 hover:text-sky-700 font-medium flex items-center gap-2">
                View All Trips →
            </a>
        </div>

        @if(isset($activeTripsList) && $activeTripsList->isNotEmpty())
            <div class="grid gap-6">
                @foreach($activeTripsList as $trip)
                    <a href="{{ route('driver.trips.show', $trip) }}" 
                       class="apple-card p-8 hover:shadow-xl transition-all block">
                        <div class="flex flex-col md:flex-row justify-between gap-6">
                            <div class="flex-1">
                                <div class="flex items-center gap-4">
                                    <span class="px-5 py-2 rounded-3xl text-sm font-medium
                                        @if($trip->isActive()) bg-emerald-100 text-emerald-700 
                                        @else bg-amber-100 text-amber-700 @endif">
                                        {{ ucfirst($trip->status) }}
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        {{ $trip->departure_time->format('D, j M • H:i') }}
                                    </span>
                                </div>
                                <p class="mt-5 text-lg font-medium">
                                    {{ $trip->requests->first()->pickup_location ?? 'N/A' }} 
                                    → 
                                    {{ $trip->requests->first()->dropoff_location ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-xl">{{ $trip->vehicle->plate_number ?? '—' }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="apple-card p-16 text-center">
                <p class="text-xl text-gray-400">No trips assigned yet</p>
                <p class="text-gray-500 mt-3">New assignments will appear here</p>
            </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Fuel Requests -->
        <a href="{{ route('driver.fuel-requests.index') }}" 
           class="apple-card p-8 hover:shadow-xl transition group flex items-center gap-6">
            <div class="w-16 h-16 bg-indigo-100 rounded-3xl flex items-center justify-center text-4xl flex-shrink-0">
                ⛽
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-xl font-semibold group-hover:text-indigo-600">Fuel Requests</h3>
                <p class="text-gray-600 mt-1">Request fuel and track approvals</p>
            </div>
        </a>

        <!-- Report Breakdown -->
        <a href="{{ route('driver.breakdowns.create') }}" 
           class="apple-card p-8 hover:shadow-xl transition group flex items-center gap-6">
            <div class="w-16 h-16 bg-red-100 rounded-3xl flex items-center justify-center text-4xl flex-shrink-0">
                ⚠️
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-xl font-semibold group-hover:text-red-600">Report Breakdown</h3>
                <p class="text-gray-600 mt-1">Report vehicle issues immediately</p>
            </div>
        </a>

        <!-- My Breakdown Reports -->
        <a href="{{ route('driver.breakdowns.index') }}" 
           class="apple-card p-8 hover:shadow-xl transition group flex items-center gap-6">
            <div class="w-16 h-16 bg-orange-100 rounded-3xl flex items-center justify-center text-4xl flex-shrink-0">
                📋
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-xl font-semibold group-hover:text-orange-600">My Breakdown Reports</h3>
                <p class="text-gray-600 mt-1">View status of reported issues</p>
            </div>
        </a>

    </div>

</div>
@endsection