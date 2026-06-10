<!DOCTYPE html>
<html>
<head>
    <title>Vehicle Tracking</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>

    <style>
        body {
            font-family: -apple-system;
            background: #f5f5f7;
        }

        #map {
            height: 600px;
            border-radius: 16px;
            margin: 20px;
        }
    </style>
</head>

<body>

<h2 style="padding:20px;">🚗 Vehicle Movement</h2>

<div id="map"></div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    var map = L.map('map').setView([0.3476, 32.5825], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    let coords = @json($locations);

    coords.forEach(loc => {
        L.marker([loc.latitude, loc.longitude])
            .addTo(map)
            .bindPopup("Speed: " + (loc.speed ?? 0));
    });
</script>

</body>
</html>