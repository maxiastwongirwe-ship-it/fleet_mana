<x-guest-layout>

<div class="max-w-3xl mx-auto p-6 text-center">

    <h1 class="text-2xl font-bold mb-6">
        Tracking Vehicle: {{ $vehicle->plate_number }}
    </h1>

    <div id="status" class="text-gray-600 mb-4">
        Requesting location permission...
    </div>

    <div id="map" class="h-96 w-full rounded-lg border hidden"></div>

</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
const token = "{{ $vehicle->tracking_token }}";
let map = null;
let marker = null;

function initMap(lat,lng){
    document.getElementById("map").classList.remove("hidden");
    map = L.map('map').setView([lat,lng],16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
        attribution:'© OpenStreetMap'
    }).addTo(map);
    marker = L.marker([lat,lng]).addTo(map);
}

function sendLocation(position){
    const lat = position.coords.latitude;
    const lng = position.coords.longitude;
    const accuracy = position.coords.accuracy;
    const speed = position.coords.speed;
    document.getElementById("status").innerText = "Location tracking active";
    if(!map) initMap(lat,lng);
    marker.setLatLng([lat,lng]);
    map.setView([lat,lng]);
    fetch("{{ route('driver.tracking.storeLocation') }}",{
        method:"POST",
        headers:{
            "Content-Type":"application/json",
            "X-CSRF-TOKEN":"{{ csrf_token() }}"
        },
        body:JSON.stringify({
            token:token,
            latitude:lat,
            longitude:lng,
            accuracy:accuracy,
            speed:speed
        })
    });
}

function locationError(error){
    document.getElementById("status").innerText =
        "Location permission denied. Please enable location and refresh.";
}

window.onload = function(){
    if(navigator.geolocation){
        navigator.geolocation.watchPosition(
            sendLocation,
            locationError,
            { enableHighAccuracy:true, maximumAge:5000, timeout:10000 }
        );
    } else {
        document.getElementById("status").innerText =
            "Geolocation is not supported by this browser.";
    }
}
</script>

</x-guest-layout>