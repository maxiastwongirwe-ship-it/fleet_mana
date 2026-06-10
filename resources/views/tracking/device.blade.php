<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Tracking</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-[#f5f5f7] min-h-screen flex items-center justify-center px-4">

<div class="bg-white w-full max-w-lg rounded-[40px] shadow-2xl p-10">

    <div class="text-center">

        <h1 class="text-3xl font-bold">Live Vehicle Tracking</h1>

        <p class="text-gray-500 mt-2">
            Vehicle: <b>{{ $vehicle->plate_number }}</b>
        </p>

        <div id="status" class="mt-6 bg-yellow-100 text-yellow-700 p-3 rounded-xl">
            Waiting for GPS...
        </div>

        <div class="grid grid-cols-2 gap-4 mt-6 text-left">

            <div class="bg-gray-100 p-3 rounded-xl">
                <p class="text-xs text-gray-500">Latitude</p>
                <p id="latitude">--</p>
            </div>

            <div class="bg-gray-100 p-3 rounded-xl">
                <p class="text-xs text-gray-500">Longitude</p>
                <p id="longitude">--</p>
            </div>

            <div class="bg-gray-100 p-3 rounded-xl">
                <p class="text-xs text-gray-500">Accuracy</p>
                <p id="accuracy">--</p>
            </div>

            <div class="bg-gray-100 p-3 rounded-xl">
                <p class="text-xs text-gray-500">Speed</p>
                <p id="speed">--</p>
            </div>

        </div>

        <div id="internet" class="mt-5 text-sm font-semibold"></div>

        <button onclick="retryGPS()" class="mt-6 bg-gray-200 px-4 py-2 rounded-xl">
            Retry GPS
        </button>

    </div>

</div>

<script>
let watchId = null;
let lastLat = null;
let lastLng = null;

const statusBox = document.getElementById('status');

function setStatus(msg, color) {
    statusBox.innerText = msg;
    statusBox.className = "mt-6 p-3 rounded-xl " + color;
}

function updateInternet() {
    const el = document.getElementById("internet");

    if (navigator.onLine) {
        el.innerHTML = "🟢 Online";
        el.className = "mt-5 text-green-600 font-semibold";
    } else {
        el.innerHTML = "🔴 Offline";
        el.className = "mt-5 text-red-600 font-semibold";
    }
}

async function sendLocation(lat, lng, acc, spd) {
    try {
        const res = await fetch("{{ route('device.track.update', $vehicle->tracking_token) }}", {
            method: "POST",
            credentials: "same-origin",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                latitude: lat,
                longitude: lng,
                accuracy: acc,
                speed: spd
            })
        });

        if (!res.ok) {
            console.error(await res.text());
        }

        return res.ok;

    } catch (err) {
        console.error("Send error:", err);
        return false;
    }
}

function updateUI(pos) {
    const { latitude, longitude, accuracy, speed } = pos.coords;

    document.getElementById("latitude").innerText = latitude.toFixed(6);
    document.getElementById("longitude").innerText = longitude.toFixed(6);
    document.getElementById("accuracy").innerText = Math.round(accuracy) + " m";
    document.getElementById("speed").innerText = (speed || 0).toFixed(2) + " m/s";
}

function hasMoved(lat, lng) {
    if (!lastLat || !lastLng) return true;

    const dist = Math.sqrt(
        Math.pow(lat - lastLat, 2) + Math.pow(lng - lastLng, 2)
    );

    return dist > 0.00001;
}

function startTracking() {
    if (watchId) navigator.geolocation.clearWatch(watchId);

    watchId = navigator.geolocation.watchPosition(async (pos) => {

        const { latitude, longitude, accuracy, speed } = pos.coords;

        updateUI(pos);

        if (!hasMoved(latitude, longitude)) return;

        lastLat = latitude;
        lastLng = longitude;

        setStatus("Tracking active ✅", "bg-green-100 text-green-700");

        if (navigator.onLine) {
            await sendLocation(latitude, longitude, accuracy, speed || 0);
        }

    }, (err) => {
        console.error(err);
        setStatus("GPS error ❌", "bg-red-100 text-red-700");
    }, {
        enableHighAccuracy: true,
        timeout: 15000,
        maximumAge: 0
    });
}

function initGPS() {
    if (!navigator.geolocation) {
        setStatus("GPS not supported", "bg-red-100 text-red-700");
        return;
    }

    setStatus("Getting GPS fix...", "bg-blue-100 text-blue-700");

    navigator.geolocation.getCurrentPosition(async (pos) => {

        updateUI(pos);

        lastLat = pos.coords.latitude;
        lastLng = pos.coords.longitude;

        if (navigator.onLine) {
            await sendLocation(
                pos.coords.latitude,
                pos.coords.longitude,
                pos.coords.accuracy,
                pos.coords.speed || 0
            );
        }

        startTracking();

    }, (err) => {
        console.error(err);
        setStatus("GPS denied or unavailable", "bg-red-100 text-red-700");
    }, {
        enableHighAccuracy: true,
        timeout: 20000,
        maximumAge: 0
    });
}

function retryGPS() {
    if (watchId) navigator.geolocation.clearWatch(watchId);
    initGPS();
}

window.addEventListener("online", updateInternet);
window.addEventListener("offline", updateInternet);

updateInternet();
initGPS();
</script>

</body>
</html>