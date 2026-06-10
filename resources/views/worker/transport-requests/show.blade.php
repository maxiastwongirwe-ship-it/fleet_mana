@extends('layouts.worker')

@section('title', 'Request Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-4xl font-semibold tracking-tight">Request Details</h1>
            <p class="text-gray-500">#{{ $transportRequest->id }}</p>
        </div>
        <a href="{{ route('worker.transport-requests.index') }}" 
           class="text-blue-600 hover:text-blue-700">← Back</a>
    </div>

    <!-- Status -->
    <div>
        <span class="inline-block px-8 py-3 rounded-3xl text-xl font-semibold
            @if($transportRequest->isPending()) bg-yellow-100 text-yellow-700
            @elseif($transportRequest->isApproved()) bg-blue-100 text-blue-700
            @elseif($transportRequest->isAssigned()) bg-emerald-100 text-emerald-700
            @elseif($transportRequest->isRejected()) bg-red-100 text-red-700
            @endif">
            {{ ucfirst($transportRequest->status) }}
        </span>
    </div>

    <!-- Map -->
    <div class="apple-card p-8">
        <h3 class="font-medium mb-4">Route Map</h3>
        <div id="route-map" class="map-container h-96"></div>
    </div>

    <!-- Request Info -->
    <div class="apple-card p-8">
        <h3 class="font-semibold text-xl mb-6">Request Information</h3>
        <div class="grid md:grid-cols-2 gap-8">
            <div>
                <p class="text-sm text-gray-500">Type</p>
                <p class="font-medium">{{ ucfirst($transportRequest->request_type) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Pickup Time</p>
                <p class="font-medium">{{ $transportRequest->pickup_time->format('l, d M Y • g:i A') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Pickup</p>
                <p class="font-medium">{{ $transportRequest->pickup_location }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Dropoff</p>
                <p class="font-medium">{{ $transportRequest->dropoff_location }}</p>
            </div>
        </div>
    </div>

    <!-- Assigned Vehicle Section - This is the key part -->
    @if($transportRequest->trips->isNotEmpty())
        <div class="apple-card p-8 bg-emerald-50 border border-emerald-100">
            <h3 class="text-2xl font-semibold mb-6 flex items-center gap-3">
                <span class="text-4xl">🚐</span> Assigned Vehicle & Driver
            </h3>

            @foreach($transportRequest->trips as $trip)
                <div class="bg-white rounded-3xl p-8">
                    <div class="flex flex-col md:flex-row gap-10">
                        <div class="flex-1">
                            <p class="text-sm text-gray-500">Vehicle</p>
                            <p class="text-3xl font-semibold">{{ $trip->vehicle->plate_number }}</p>
                            <p class="text-gray-600">{{ $trip->vehicle->make ?? '' }} {{ $trip->vehicle->model ?? '' }}</p>
                            
                            <div class="mt-6 grid grid-cols-2 gap-6">
                                <div>
                                    <p class="text-sm text-gray-500">Capacity</p>
                                    <p class="font-medium">{{ $trip->vehicle->capacity ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status</p>
                                    <span class="px-5 py-2 bg-emerald-100 text-emerald-700 rounded-3xl text-sm">Assigned</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex-1">
                            <p class="text-sm text-gray-500">Driver</p>
                            <p class="text-3xl font-semibold">{{ $trip->driver->name ?? 'Not Assigned' }}</p>
                            <p class="text-emerald-600 font-medium mt-2">Ready for pickup</p>
                        </div>
                    </div>

                    <div class="mt-10 pt-8 border-t">
                        <p class="text-sm text-gray-500">Departure Time</p>
                        <p class="text-2xl font-medium">{{ $trip->departure_time->format('l, d M Y • H:i') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="apple-card p-12 text-center">
            <p class="text-gray-500">Waiting for admin to assign a vehicle...</p>
        </div>
    @endif
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('route-map').setView([0.3476, 32.5825], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    @if($transportRequest->pickup_lat)
        L.marker([{{ $transportRequest->pickup_lat }}, {{ $transportRequest->pickup_lng }}])
            .addTo(map)
            .bindPopup('Pickup Location');
    @endif

    @if($transportRequest->dropoff_lat)
        L.marker([{{ $transportRequest->dropoff_lat }}, {{ $transportRequest->dropoff_lng }}])
            .addTo(map)
            .bindPopup('Dropoff Location');
    @endif
});
</script>
@endsection