@extends('layouts.app')

@section('content')

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet/dist/leaflet.css"
/>

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css"
/>

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css"
/>

<style>

    #map {
        height: 85vh;
        border-radius: 30px;
    }

    .leaflet-popup-content-wrapper {
        border-radius: 20px;
    }

</style>

<div class="min-h-screen bg-[#f5f5f7] p-8">

    <div class="max-w-7xl mx-auto">

        <div class="flex justify-between items-center mb-6">

            <div>

                <h1 class="text-4xl font-bold text-gray-900">
                    Live Fleet Tracking
                </h1>

                <p class="text-gray-500 mt-2">
                    Real-time vehicle tracking
                </p>

            </div>

            <div class="bg-white rounded-3xl px-6 py-4 shadow-sm">

                <span class="text-gray-500">
                    Refresh:
                </span>

                <span class="font-bold">
                    30 Seconds
                </span>

            </div>

        </div>

        <div class="bg-white rounded-[35px] p-4 shadow-sm">

            <div id="map"></div>

        </div>

    </div>

</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>

<script>

const map = L.map('map').setView(
    [0.3476, 32.5825],
    7
);

L.tileLayer(
    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    {
        attribution:
            '&copy; OpenStreetMap contributors'
    }
).addTo(map);

const markers = L.markerClusterGroup();

map.addLayer(markers);

async function loadVehicles()
{
    try {

        const response = await fetch(
            "{{ route('admin.tracking.locations') }}"
        );

        const data = await response.json();

        markers.clearLayers();

        data.forEach(location => {

            if(location.latitude && location.longitude)
            {
                const marker = L.marker([
                    location.latitude,
                    location.longitude
                ]);

                marker.bindPopup(`

                    <div style="min-width:220px">

                        <h2 style="
                            font-size:18px;
                            font-weight:bold;
                            margin-bottom:10px;
                        ">
                            ${location.vehicle?.plate_number ?? 'Vehicle'}
                        </h2>

                        <p>
                            Latitude:
                            ${location.latitude}
                        </p>

                        <p>
                            Longitude:
                            ${location.longitude}
                        </p>

                        <p>
                            Speed:
                            ${location.speed ?? 'N/A'}
                        </p>

                    </div>

                `);

                markers.addLayer(marker);
            }

        });

    } catch(error)
    {
        console.log(error);
    }
}

loadVehicles();

setInterval(() => {

    loadVehicles();

}, 30000);

</script>

@endsection