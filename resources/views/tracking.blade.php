@extends('layouts.app')

@section('title', 'Vehicle Tracking')

@section('content')
    <div class="max-w-md mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Vehicle Tracking</h1>

        <p class="mb-4">Welcome, {{ auth()->user()->name }}. Your location will be shared periodically.</p>

        <button id="start-tracking" class="px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700">
            Start Tracking
        </button>
        <button id="stop-tracking" class="px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 hidden">
            Stop Tracking
        </button>

        <p id="status" class="mt-4 text-gray-600">Status: Not tracking</p>
    </div>

    <script>
        let watchId;
        const token = '{{ auth()->user()->tracking_token }}';  // assume token is passed or stored

        document.getElementById('start-tracking').addEventListener('click', () => {
            if (navigator.geolocation) {
                watchId = navigator.geolocation.watchPosition(sendLocation, showError, { enableHighAccuracy: true });
                document.getElementById('status').textContent = 'Tracking started';
                document.getElementById('start-tracking').classList.add('hidden');
                document.getElementById('stop-tracking').classList.remove('hidden');
            } else {
                document.getElementById('status').textContent = 'Geolocation not supported';
            }
        });

        document.getElementById('stop-tracking').addEventListener('click', () => {
            navigator.geolocation.clearWatch(watchId);
            document.getElementById('status').textContent = 'Tracking stopped';
            document.getElementById('stop-tracking').classList.add('hidden');
            document.getElementById('start-tracking').classList.remove('hidden');
        });

        function sendLocation(position) {
            fetch('/api/location/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    token: token,
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                    speed: position.coords.speed,
                }),
            })
            .then(response => response.json())
            .then(data => {
                console.log('Location sent successfully');
            })
            .catch(error => {
                console.error('Error sending location', error);
            });
        }

        function showError(error) {
            document.getElementById('status').textContent = 'Error: ' + error.message;
        }
    </script>
@endsection