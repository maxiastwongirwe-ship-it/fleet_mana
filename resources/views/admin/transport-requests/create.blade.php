@extends('layouts.admin')

@section('title', 'New Transport Request')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-2">Create New Transport Request</h1>
    <p class="text-gray-600 mb-8">Admin can create requests on behalf of workers</p>

    <div class="bg-white rounded-3xl shadow p-8">
        <form method="POST" action="{{ route('admin.transport-requests.store') }}">
            @csrf

            <!-- Request Type -->
            <div class="mb-8">
                <label class="block text-lg font-medium mb-4">Request Type</label>
                <div class="flex gap-6">
                    <label class="inline-flex items-center">
                        <input type="radio" name="request_type" value="passenger" checked class="h-5 w-5">
                        <span class="ml-3">👥 Passenger / Workers</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="request_type" value="goods" class="h-5 w-5">
                        <span class="ml-3">📦 Goods / Cargo</span>
                    </label>
                </div>
            </div>

            <!-- Requested By -->
            <div class="mb-8">
                <label class="block text-lg font-medium mb-3">Requested By</label>
                <select name="requested_by" required class="w-full px-5 py-4 border border-gray-300 rounded-2xl">
                    <option value="">Select worker</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Maps -->
            <div class="grid md:grid-cols-2 gap-8 mb-8">
                <div>
                    <label class="block text-sm font-medium mb-3">Pickup Location</label>
                    <div id="pickup-map" class="h-80 rounded-3xl border border-gray-200 overflow-hidden"></div>
                    <input type="hidden" name="pickup_location" id="pickup_location" required>
                    <input type="hidden" name="pickup_lat" id="pickup_lat">
                    <input type="hidden" name="pickup_lng" id="pickup_lng">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-3">Dropoff Location</label>
                    <div id="dropoff-map" class="h-80 rounded-3xl border border-gray-200 overflow-hidden"></div>
                    <input type="hidden" name="dropoff_location" id="dropoff_location" required>
                    <input type="hidden" name="dropoff_lat" id="dropoff_lat">
                    <input type="hidden" name="dropoff_lng" id="dropoff_lng">
                </div>
            </div>

            <!-- Pickup Time & Purpose -->
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-3">Pickup Time</label>
                    <input type="datetime-local" name="pickup_time" required 
                           class="w-full px-5 py-4 border border-gray-300 rounded-2xl">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-3">Purpose / Notes</label>
                    <textarea name="purpose" rows="4" 
                              class="w-full px-5 py-4 border border-gray-300 rounded-2xl"></textarea>
                </div>
            </div>

            <!-- Passengers Section -->
            <div id="passenger-section" class="mt-10">
                <div class="flex justify-between mb-4">
                    <h3 class="text-lg font-medium">Passengers</h3>
                    <button type="button" id="add-passenger" 
                            class="text-indigo-600 hover:text-indigo-700">+ Add Passenger</button>
                </div>
                <div id="passenger-container" class="space-y-4"></div>
            </div>

            <div class="mt-12">
                <button type="submit" 
                        class="w-full py-5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-3xl">
                    Create Transport Request
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
let pickupMarker = null, dropoffMarker = null;

function initMap(containerId, latId, lngId, locationId) {
    const map = L.map(containerId).setView([0.3476, 32.5825], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    map.on('click', function(e) {
        if (containerId === 'pickup-map') {
            if (pickupMarker) map.removeLayer(pickupMarker);
            pickupMarker = L.marker(e.latlng, {draggable: true}).addTo(map);
        } else {
            if (dropoffMarker) map.removeLayer(dropoffMarker);
            dropoffMarker = L.marker(e.latlng, {draggable: true}).addTo(map);
        }

        document.getElementById(latId).value = e.latlng.lat.toFixed(8);
        document.getElementById(lngId).value = e.latlng.lng.toFixed(8);
        document.getElementById(locationId).value = `${e.latlng.lat.toFixed(6)}, ${e.latlng.lng.toFixed(6)}`;
    });
}

window.onload = () => {
    initMap('pickup-map', 'pickup_lat', 'pickup_lng', 'pickup_location');
    initMap('dropoff-map', 'dropoff_lat', 'dropoff_lng', 'dropoff_location');

    // Passenger JS remains same as your original
};
</script>
@endsection