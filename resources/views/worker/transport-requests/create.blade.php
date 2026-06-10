@extends('layouts.worker')

@section('title', 'New Request')

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="text-4xl font-semibold tracking-tight">Book a Vehicle</h1>
        <p class="text-gray-600 mt-2">Click on the maps to set exact pickup and dropoff locations</p>
    </div>

    <div class="apple-card p-8">
        <form method="POST" action="{{ route('worker.transport-requests.store') }}" id="booking-form">
            @csrf

            <!-- Request Type -->
            <div class="mb-10">
                <div class="inline-flex bg-gray-100 rounded-3xl p-1 w-full max-w-md mx-auto">
                    <button type="button" onclick="setType('passenger')" id="btn-passenger"
                            class="flex-1 py-4 rounded-3xl font-semibold transition-all">👥 Passengers</button>
                    <button type="button" onclick="setType('goods')" id="btn-goods"
                            class="flex-1 py-4 rounded-3xl font-semibold transition-all">📦 Goods</button>
                </div>
                <input type="hidden" name="request_type" id="request_type" value="passenger">
            </div>

            <!-- Maps -->
            <div class="grid md:grid-cols-2 gap-8 mb-10">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Pickup Location</label>
                    <div id="pickup-map" class="map-container"></div>
                    <input type="hidden" name="pickup_location" id="pickup_location" required>
                    <input type="hidden" name="pickup_lat" id="pickup_lat">
                    <input type="hidden" name="pickup_lng" id="pickup_lng">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Dropoff Location</label>
                    <div id="dropoff-map" class="map-container"></div>
                    <input type="hidden" name="dropoff_location" id="dropoff_location" required>
                    <input type="hidden" name="dropoff_lat" id="dropoff_lat">
                    <input type="hidden" name="dropoff_lng" id="dropoff_lng">
                </div>
            </div>

            <!-- Other Fields -->
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm font-medium text-gray-700">Pickup Date & Time</label>
                    <input type="datetime-local" name="pickup_time" required 
                           class="w-full mt-2 rounded-3xl border border-gray-200 py-4 px-6 focus:border-blue-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Purpose / Notes</label>
                    <textarea name="purpose" rows="3" 
                              class="w-full mt-2 rounded-3xl border border-gray-200 py-4 px-6"></textarea>
                </div>
            </div>

            <!-- Passengers -->
            <div id="passengers-section" class="mt-10">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-lg">Passengers</h3>
                    <button type="button" onclick="addPassenger()" 
                            class="text-blue-600 hover:text-blue-700 flex items-center gap-1 text-sm font-medium">
                        + Add another
                    </button>
                </div>
                <div id="passengers-list" class="space-y-4"></div>
            </div>

            <div class="mt-12 pt-6 border-t">
                <button type="submit" 
                        class="w-full py-6 bg-black hover:bg-gray-900 text-white text-xl font-semibold rounded-3xl active:scale-[0.97] transition-all">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Leaflet Maps
let pickupMap, dropoffMap;
let pickupMarker = null;
let dropoffMarker = null;

function initMap(containerId, latField, lngField, locationField) {
    const map = L.map(containerId).setView([0.3476, 32.5825], 12); // Kampala area (Uganda)

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    map.on('click', function(e) {
        if (containerId === 'pickup-map') {
            if (pickupMarker) map.removeLayer(pickupMarker);
            pickupMarker = L.marker(e.latlng, { draggable: true }).addTo(map);
        } else {
            if (dropoffMarker) map.removeLayer(dropoffMarker);
            dropoffMarker = L.marker(e.latlng, { draggable: true }).addTo(map);
        }

        latField.value = e.latlng.lat.toFixed(8);
        lngField.value = e.latlng.lng.toFixed(8);
        locationField.value = `${e.latlng.lat.toFixed(6)}, ${e.latlng.lng.toFixed(6)}`;

        // Update on drag
        (containerId === 'pickup-map' ? pickupMarker : dropoffMarker).on('dragend', function(ev) {
            const pos = ev.target.getLatLng();
            latField.value = pos.lat.toFixed(8);
            lngField.value = pos.lng.toFixed(8);
            locationField.value = `${pos.lat.toFixed(6)}, ${pos.lng.toFixed(6)}`;
        });
    });

    return map;
}

window.onload = () => {
    // Initialize maps
    pickupMap = initMap('pickup-map', 
        document.getElementById('pickup_lat'), 
        document.getElementById('pickup_lng'), 
        document.getElementById('pickup_location'));

    dropoffMap = initMap('dropoff-map', 
        document.getElementById('dropoff_lat'), 
        document.getElementById('dropoff_lng'), 
        document.getElementById('dropoff_location'));

    setType('passenger');
    addPassenger(true); // Add requester automatically
};

function setType(type) {
    document.getElementById('request_type').value = type;
    document.getElementById('passengers-section').style.display = type === 'passenger' ? 'block' : 'none';

    document.getElementById('btn-passenger').classList.toggle('bg-white', type === 'passenger');
    document.getElementById('btn-goods').classList.toggle('bg-white', type === 'goods');
}

let passengerIndex = 1;

function addPassenger(isRequester = false) {
    const list = document.getElementById('passengers-list');
    const div = document.createElement('div');
    div.className = 'flex gap-4 items-center bg-gray-50 rounded-3xl p-5';

    if (isRequester) {
        div.innerHTML = `
            <input type="text" value="{{ Auth::user()->name }}" readonly 
                   class="flex-1 bg-white rounded-3xl border border-gray-200 py-4 px-6 text-gray-700">
            <span class="text-green-600 font-medium">You</span>
        `;
    } else {
        div.innerHTML = `
            <input type="text" name="passengers[${passengerIndex}][name]" 
                   placeholder="Passenger full name" required
                   class="flex-1 rounded-3xl border border-gray-200 py-4 px-6">
            <button type="button" onclick="this.parentElement.remove()" 
                    class="text-red-500 hover:text-red-600 px-4">Remove</button>
        `;
        passengerIndex++;
    }
    list.appendChild(div);
}
</script>
@endsection