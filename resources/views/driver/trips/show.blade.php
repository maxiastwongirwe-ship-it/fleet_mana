@extends('layouts.driver')

@section('title', 'Trip #{{ $trip->id }}')

@section('content')
<div class="space-y-10">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-4xl font-semibold tracking-tight">Trip #{{ $trip->id }}</h1>
            <p class="text-gray-600 mt-1">Assigned to you</p>
        </div>
        <span class="px-8 py-3 rounded-3xl text-lg font-medium
            @if($trip->isActive()) bg-emerald-100 text-emerald-700
            @elseif($trip->isCompleted()) bg-gray-100 text-gray-700
            @else bg-amber-100 text-amber-700 @endif">
            {{ ucfirst($trip->status) }}
        </span>
    </div>

    <!-- Map -->
    <div class="apple-card p-8">
        <h2 class="text-2xl font-semibold mb-6">Route Map</h2>
        <div id="trip-map" class="map-container"></div>
    </div>

    <!-- Trip Information -->
    <div class="grid md:grid-cols-2 gap-6">
        <div class="apple-card p-8">
            <h3 class="font-semibold text-lg mb-4">Vehicle</h3>
            <p class="text-3xl font-medium">{{ $trip->vehicle->plate_number ?? '—' }}</p>
            <p class="text-gray-600">{{ $trip->vehicle->make ?? '' }} {{ $trip->vehicle->model ?? '' }}</p>
        </div>

        <div class="apple-card p-8">
            <h3 class="font-semibold text-lg mb-4">Departure Time</h3>
            <p class="text-3xl font-medium">{{ $trip->departure_time->format('d M Y') }}</p>
            <p class="text-2xl text-gray-600">{{ $trip->departure_time->format('H:i') }}</p>
        </div>
    </div>

    <!-- Requests / Route Details -->
    <div class="apple-card p-8">
        <h3 class="font-semibold text-lg mb-6">Route Details</h3>
        @foreach($trip->requests as $request)
            <div class="mb-8 last:mb-0 p-6 bg-gray-50 rounded-3xl">
                <p class="font-medium">Request #{{ $request->id }} — {{ ucfirst($request->request_type) }}</p>
                
                <div class="mt-6 grid md:grid-cols-2 gap-8">
                    <div>
                        <p class="text-sm text-gray-500">Pickup</p>
                        <p class="font-medium mt-1">{{ $request->pickup_location }}</p>
                        <p class="text-xs text-gray-600 mt-2">
                            {{ $request->pickup_lat ?? '—' }}, {{ $request->pickup_lng ?? '—' }}
                        </p>
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $request->pickup_lat }},{{ $request->pickup_lng }}" 
                           target="_blank" class="text-sky-600 text-sm hover:underline block mt-2">
                            Open in Google Maps →
                        </a>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Dropoff</p>
                        <p class="font-medium mt-1">{{ $request->dropoff_location }}</p>
                        <p class="text-xs text-gray-600 mt-2">
                            {{ $request->dropoff_lat ?? '—' }}, {{ $request->dropoff_lng ?? '—' }}
                        </p>
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $request->dropoff_lat }},{{ $request->dropoff_lng }}" 
                           target="_blank" class="text-sky-600 text-sm hover:underline block mt-2">
                            Open in Google Maps →
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Trip Actions - Made prominent and always visible -->
    <div class="apple-card p-10 bg-white">
        <h3 class="font-semibold text-2xl mb-8">Trip Actions</h3>
        
        <div class="grid gap-6">
            @if($trip->canStartTrip())
                <form method="POST" action="{{ route('driver.trips.start', $trip) }}">
                    @csrf
                    <button type="submit" 
                            class="w-full py-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-3xl text-xl transition">
                        🚀 Start Trip Now
                    </button>
                </form>
            @endif

            @if($trip->canFinishTrip())
                <form method="POST" action="{{ route('driver.trips.finish', $trip) }}">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Actual Arrival Time</label>
                        <input type="datetime-local" name="actual_arrival_time" required 
                               class="w-full px-6 py-5 border border-gray-300 rounded-3xl text-lg">
                    </div>
                    <button type="submit" 
                            class="w-full py-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-3xl text-xl transition">
                        ✅ Mark Trip as Completed
                    </button>
                </form>
            @endif
        </div>
    </div>

</div>

<!-- Leaflet Map -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('trip-map').setView([0.3476, 32.5825], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    @foreach($trip->requests as $request)
        @if($request->pickup_lat && $request->pickup_lng)
            L.marker([{{ $request->pickup_lat }}, {{ $request->pickup_lng }}])
                .addTo(map)
                .bindPopup('Pickup: {{ addslashes($request->pickup_location) }}');
        @endif

        @if($request->dropoff_lat && $request->dropoff_lng)
            L.marker([{{ $request->dropoff_lat }}, {{ $request->dropoff_lng }}])
                .addTo(map)
                .bindPopup('Dropoff: {{ addslashes($request->dropoff_location) }}');
        @endif
    @endforeach
});
</script>
@endsection