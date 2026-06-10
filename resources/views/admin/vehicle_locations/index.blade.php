<!DOCTYPE html>
<html>
<head>
    <title>Vehicle Locations</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f5f5f7;
            margin: 0;
        }

        .container {
            padding: 20px;
        }

        h2 {
            font-weight: 600;
            margin-bottom: 15px;
        }

        #map {
            height: 600px;
            border-radius: 20px;
        }

        .leaflet-tooltip.my-label {
            background: white;
            color: black;
            font-weight: bold;
            border-radius: 4px;
            padding: 2px 6px;
            font-size: 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🚗 Live Vehicle Locations</h2>
    <div id="map"></div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<script>
    var map = L.map('map').setView([0.3476, 32.5825], 10); // Kampala default
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    let markers = [];

    function createCarIcon() {
        return L.divIcon({
            className: '',
            html: '<i class="fas fa-car-alt" style="font-size:28px; color:red;"></i>',
            iconSize: [28, 28],
            iconAnchor: [14, 14]
        });
    }

    function loadVehicles() {
        fetch("/vehicle-locations-data") // your backend route returning latest per vehicle
        .then(res => res.json())
        .then(data => {

            // Remove old markers
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];

            // Ensure latest location per vehicle
            let latestPerVehicle = {};
            data.forEach(loc => {
                if (!latestPerVehicle[loc.vehicle_id] || new Date(loc.created_at) > new Date(latestPerVehicle[loc.vehicle_id].created_at)) {
                    latestPerVehicle[loc.vehicle_id] = loc;
                }
            });

            Object.values(latestPerVehicle).forEach(loc => {
                let time = new Date(loc.created_at).toLocaleString();
                let plate = loc.vehicle ? loc.vehicle.plate_number : 'N/A';

                // Marker with red car icon
                let marker = L.marker([loc.latitude, loc.longitude], {icon: createCarIcon()})
                    .addTo(map)
                    .bindPopup(`
                        <div style="font-size:14px">
                            <b>Plate:</b> ${plate} <br>
                            <b>Last Update:</b> ${time}
                        </div>
                    `);

                // Always show number plate
                marker.bindTooltip(plate, {permanent: true, direction: "top", className: "my-label"})
                    .openTooltip();

                markers.push(marker);
            });

        })
        .catch(err => console.error(err));
    }

    loadVehicles();
    setInterval(loadVehicles, 5000); // refresh every 5 seconds
</script>

</body>
</html>