<!DOCTYPE html>
<html>
<head>
    <title>Driver Tracking</title>

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "San Francisco", Arial;
            text-align: center;
            background: #f5f5f7;
            padding: 40px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            max-width: 400px;
            margin: auto;
        }

        button {
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            background: black;
            color: white;
            font-weight: 600;
            cursor: pointer;
        }

        .status {
            margin-top: 15px;
            font-size: 14px;
            color: gray;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Vehicle Tracking</h2>
    <p>Driver: {{ $driver->user->name }}</p>

    <button onclick="startTracking()">Enable Location</button>

    <p class="status" id="status">Waiting for permission...</p>
</div>

<script>
let tracking = false;

function startTracking() {
    const status = document.getElementById('status');

    if (!navigator.geolocation) {
        status.innerText = "❌ Geolocation not supported by your device.";
        return;
    }

    status.innerText = "Requesting location access...";

    navigator.geolocation.getCurrentPosition(
        function(position) {
            status.innerText = "✅ Location access granted. Tracking started.";
            tracking = true;
            sendLocation();
        },
        function(error) {
            status.innerText = "❌ Permission denied or unavailable.";
        }
    );
}

function sendLocation() {
    if (!tracking) return;

    navigator.geolocation.watchPosition(
        function(position) {

            fetch('/driver-track/{{ $token }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                    speed: position.coords.speed
                })
            })
            .then(res => res.json())
            .then(data => console.log("Sent:", data))
            .catch(err => console.error("Error:", err));

        },
        function(error) {
            document.getElementById('status').innerText = "❌ Error getting location.";
        },
        {
            enableHighAccuracy: true,
            maximumAge: 0,
            timeout: 5000
        }
    );
}
</script>

</body>
</html>