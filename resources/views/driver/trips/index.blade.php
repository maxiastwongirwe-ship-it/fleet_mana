@extends('layouts.driver')

@section('title', 'My Trips')

@section('content')
<div class="space-y-8">

    <div class="flex justify-between items-center">
        <h1 class="text-4xl font-semibold tracking-tight">My Trips</h1>
        <a href="{{ route('driver.dashboard') }}" 
           class="text-sky-600 hover:text-sky-700 font-medium">← Back to Dashboard</a>
    </div>

    @if($trips->isNotEmpty())
        <div class="grid gap-6">
            @foreach($trips as $trip)
                <a href="{{ route('driver.trips.show', $trip) }}" 
                   class="apple-card p-8 hover:shadow-xl transition-all block group">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex-1">
                            <div class="flex items-center gap-4">
                                <span class="inline-flex px-5 py-2 rounded-3xl text-sm font-medium
                                    @if($trip->isActive()) bg-emerald-100 text-emerald-700
                                    @elseif($trip->isScheduled()) bg-amber-100 text-amber-700
                                    @elseif($trip->isCompleted()) bg-gray-100 text-gray-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ ucfirst($trip->status) }}
                                </span>
                                <span class="text-gray-500">
                                    {{ $trip->departure_time->format('D, j M • H:i') }}
                                </span>
                            </div>

                            <div class="mt-5">
                                <p class="text-xl font-medium">
                                    {{ $trip->requests->first()->pickup_location ?? 'N/A' }} 
                                    <span class="text-gray-400 mx-3">→</span> 
                                    {{ $trip->requests->first()->dropoff_location ?? 'N/A' }}
                                </p>
                            </div>

                            <p class="mt-3 text-sm text-gray-600 line-clamp-2">
                                {{ $trip->requests->first()->purpose ?? 'No purpose provided' }}
                            </p>
                        </div>

                        <div class="text-right md:text-left">
                            <p class="font-semibold text-2xl">{{ $trip->vehicle->plate_number ?? '—' }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $trip->vehicle->make ?? '' }} {{ $trip->vehicle->model ?? '' }}
                            </p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $trips->links() }}
        </div>

    @else
        <div class="apple-card p-20 text-center">
            <p class="text-2xl text-gray-400">No trips assigned to you yet.</p>
            <p class="text-gray-500 mt-4">New assignments will appear here once the admin assigns them.</p>
        </div>
    @endif

</div>
@endsection