@extends('layouts.app')

@section('title', 'Live Vehicle Tracking')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h1 class="text-3xl font-bold text-center mb-6">🚛 Live Tracking Active</h1>

        <div class="text-center mb-8">
            <p class="text-lg font-medium text-gray-700">
                Vehicle: <strong>{{ $vehicle->plate_number }}</strong>
            </p>
            <p id="status" class="mt-3 text-xl font-semibold text-green-600">
                ● Tracking Active
            </p>
            <p id="last-update" class="mt-2 text-sm text-gray-500">Last sent: Never</p>
            <p id="accuracy-speed" class="mt-1 text-sm text-gray-600">Accuracy: — | Speed: —</p>
        </div>

        <div id="feedback" class="min-h-[3rem] text-center text-sm mb-6 px-4 py-3 rounded-lg hidden"></div>

        <div class="bg-yellow-50 border border-yellow-200 p-5 rounded-xl text-sm mb-8">
            <strong>Instructions:</strong><br>
            Keep this page open in the browser.<br>
            Phone must stay plugged in and have internet.
        </div>

        <button id="stop-btn" 
                class="w-full py-4 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700">
            Stop Tracking
        </button>
    </div>
</div>

<script>
    let watchId = null;
    const token = '{{ $vehicle->tracking_token }}';

    function showFeedback(text, type = 'info') {
        const fb = document.getElementById('feedback');
        fb.className = `min-h-[3rem] text-center text-sm mb-6 px-4 py-3 rounded-lg block ${
            type === 'success' ? 'bg-green-50 text-green-800' : 
            type === 'error' ? 'bg-red-50 text-red-800' : 'bg-blue-50 text-blue-800'
        }`;
        fb.textContent = text;
    }

    function successCallback(position) {
        const data = {
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
            accuracy: position.coords.accuracy || null,
            speed: position.coords.speed || null,
            _token: '{{ csrf_token() }}'
        };

        fetch(`/tracking/${token}/location`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                const now = new Date().toLocaleTimeString();
                document.getElementById('last-update').textContent = `Last sent: ${now}`;
                document.getElementById('accuracy-speed').textContent = 
                    `Accuracy: ${data.accuracy ? Math.round(data.accuracy) + 'm' : '—'} | Speed: ${data.speed ? (data.speed * 3.6).toFixed(1) + ' km/h' : '—'}`;
            }
        })
        .catch(() => {});
    }

    function errorCallback(err) {
        showFeedback('Location error: ' + err.message, 'error');
    }

    // Start tracking immediately when page loads
    window.onload = () => {
        if (!navigator.geolocation) {
            showFeedback('Geolocation not supported', 'error');
            return;
        }

        watchId = navigator.geolocation.watchPosition(
            successCallback,
            errorCallback,
            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            }
        );

        showFeedback('Background tracking started. Keep this page open.', 'success');
    };

    // Stop button
    document.getElementById('stop-btn').addEventListener('click', () => {
        if (watchId) navigator.geolocation.clearWatch(watchId);
        showFeedback('Tracking stopped.', 'error');
    });
</script>
@endsection