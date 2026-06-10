@extends('layouts.worker')

@section('title', 'Assigned Vehicle')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-semibold tracking-tight">Assigned Vehicle</h1>
            <p class="text-gray-600">Your transport request has been assigned</p>
        </div>
        <a href="{{ route('worker.transport-requests.show', $transportRequest) }}" 
           class="text-blue-600 hover:text-blue-700 font-medium">← Back to Request</a>
    </div>

    @if($transportRequest->trips->isNotEmpty())
        @foreach($transportRequest->trips as $trip)
            <div class="apple-card p-10">
                <!-- Vehicle Info -->
                <div class="flex flex-col md:flex-row gap-10">
                    <div class="flex-1">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center text-4xl">
                                🚐
                            </div>
                            <div>
                                <h2 class="text-3xl font-semibold">{{ $trip->vehicle->plate_number }}</h2>
                                <p class="text-gray-600">{{ $trip->vehicle->make }} {{ $trip->vehicle->model }} • {{ ucfirst($trip->vehicle->type ?? '') }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-8">
                            <div>
                                <p class="text-sm text-gray-500">Capacity</p>
                                <p class="font-medium text-xl">{{ $trip->vehicle->capacity }} seats/passengers</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status</p>
                                <span class="inline-block px-6 py-2 bg-emerald-100 text-emerald-700 rounded-3xl text-sm font-medium">
                                    Available & Assigned
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Driver Info -->
                    <div class="flex-1 bg-gray-50 rounded-3xl p-8">
                        <h3 class="font-semibold text-lg mb-4">Driver Information</h3>
                        <div class="flex items-center gap-5">
                            <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center text-3xl">
                                👨‍✈️
                            </div>
                            <div>
                                <p class="font-semibold text-xl">{{ $trip->driver->name ?? 'Not Assigned' }}</p>
                                <p class="text-gray-600">Professional Driver</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trip Details -->
                <div class="mt-12 pt-10 border-t grid md:grid-cols-3 gap-8">
                    <div>
                        <p class="text-sm text-gray-500">Departure Time</p>
                        <p class="font-medium text-2xl mt-2">{{ $trip->departure_time->format('l, d M Y') }}</p>
                        <p class="text-2xl text-gray-600">{{ $trip->departure_time->format('H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Pickup Location</p>
                        <p class="font-medium">{{ $transportRequest->pickup_location }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Dropoff Location</p>
                        <p class="font-medium">{{ $transportRequest->dropoff_location }}</p>
                    </div>
                </div>

                <!-- Map (Optional - shows pickup to dropoff) -->
                <div class="mt-12">
                    <h3 class="font-medium mb-4">Route Overview</h3>
                    <div id="trip-map" class="h-80 rounded-3xl border border-gray-200"></div>
                </div>
            </div>
        @endforeach
    @else
        <div class="apple-card p-20 text-center">
            <p class="text-2xl text-gray-400">No vehicle assigned yet.</p>
        </div>
    @endif
</div>

<!-- Leaflet Map -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('trip-map').setView([0.3476, 32.5825], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    @if($transportRequest->pickup_lat && $transportRequest->dropoff_lat)
        L.marker([{{ $transportRequest->pickup_lat }}, {{ $transportRequest->pickup_lng }}])
            .addTo(map)
            .bindPopup('Pickup');
        
        L.marker([{{ $transportRequest->dropoff_lat }}, {{ $transportRequest->dropoff_lng }}])
            .addTo(map)
            .bindPopup('Dropoff');
    @endif
});
</script>
@endsection