@extends('layouts.app')

@section('title', 'Track My Location')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h1 class="text-3xl font-bold text-center mb-6">Vehicle Location Tracking</h1>

        <div class="text-center mb-8">
            <p class="text-lg font-medium text-gray-700">
                Hello, <strong>{{ auth()->user()->name }}</strong>!
            </p>
            <p id="status" class="mt-3 text-xl font-semibold text-gray-600">
                Status: Not tracking
            </p>
            <p id="last-update" class="mt-2 text-sm text-gray-500">
                Last sent: Never
            </p>
            <p id="accuracy-speed" class="mt-1 text-sm text-gray-600">
                Accuracy: — | Speed: —
            </p>
        </div>

        <!-- Feedback & Errors -->
        <div id="feedback" class="min-h-[3rem] text-center text-sm mb-6 px-4 py-3 rounded-lg"
             style="display:none;">
        </div>

        <!-- Buttons -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10">
            <button id="start-btn"
                    class="px-8 py-5 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition shadow">
                Start Sharing Location
            </button>

            <button id="stop-btn" class="hidden px-8 py-5 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition shadow">
                Stop Sharing
            </button>
        </div>

        <!-- Share / Copy Controls -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <button id="share-btn"
                    class="px-6 py-4 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition flex items-center justify-center gap-2 shadow">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367 2.684m-5.367-2.684l-6.632 3.316" />
                </svg>
                Share Live Link
            </button>

            <button id="copy-btn"
                    class="px-6 py-4 bg-gray-100 text-gray-800 rounded-xl hover:bg-gray-200 transition flex items-center justify-center gap-2 shadow">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                Copy Link
            </button>
        </div>

        <!-- Instructions / Help -->
        <div class="mt-10 text-center text-sm text-gray-600">
            <p>Make sure location services are enabled on your device.</p>
            <p class="mt-2">This page must stay open for tracking to continue.</p>
        </div>
    </div>
</div>

<script>
    let watchId = null;
    const token = '{{ $vehicle->tracking_token }}'; // passed from controller
    const feedback = document.getElementById('feedback');
    const statusEl = document.getElementById('status');
    const lastUpdateEl = document.getElementById('last-update');
    const accSpeedEl = document.getElementById('accuracy-speed');

    const startBtn = document.getElementById('start-btn');
    const stopBtn = document.getElementById('stop-btn');
    const shareBtn = document.getElementById('share-btn');
    const copyBtn = document.getElementById('copy-btn');

    // ────────────────────────────────────────────────
    // Start Tracking
    // ────────────────────────────────────────────────
    startBtn.addEventListener('click', () => {
        if (!navigator.geolocation) {
            showFeedback('Geolocation is not supported by your browser.', 'error');
            return;
        }

        statusEl.textContent = 'Requesting location permission...';
        feedback.style.display = 'block';
        feedback.className = 'bg-blue-50 text-blue-800 px-4 py-3 rounded-lg';
        feedback.textContent = 'Waiting for permission...';

        watchId = navigator.geolocation.watchPosition(
            successCallback,
            errorCallback,
            {
                enableHighAccuracy: true,      // better accuracy (uses GPS)
                timeout: 10000,                // 10 seconds timeout
                maximumAge: 0                  // always get fresh position
            }
        );

        startBtn.classList.add('hidden');
        stopBtn.classList.remove('hidden');
    });

    // ────────────────────────────────────────────────
    // Success: Send location to server
    // ────────────────────────────────────────────────
    function successCallback(position) {
        const data = {
            token: token,
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
            accuracy: position.coords.accuracy || null,
            speed: position.coords.speed || null,
            _token: '{{ csrf_token() }}'
        };

        fetch('/tracking/' + token + '/location', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) throw new Error('Server error: ' + response.status);
            return response.json();
        })
        .then(result => {
            if (result.success) {
                const now = new Date().toLocaleTimeString();
                lastUpdateEl.textContent = 'Last sent: ' + now;
                accSpeedEl.textContent = `Accuracy: ${data.accuracy ? data.accuracy + ' m' : '—'} | Speed: ${data.speed ? (data.speed * 3.6).toFixed(1) + ' km/h' : '—'}`;
                showFeedback('Location sent successfully (' + now + ')', 'success');
            } else {
                showFeedback('Failed to save location: ' + (result.error || 'Unknown'), 'error');
            }
        })
        .catch(err => {
            showFeedback('Error sending location: ' + err.message, 'error');
        });
    }

    // ────────────────────────────────────────────────
    // Error handling (permission, timeout, etc.)
    // ────────────────────────────────────────────────
    function errorCallback(error) {
        let message = '';
        switch(error.code) {
            case error.PERMISSION_DENIED:
                message = 'Location access denied. Please enable location services and grant permission.';
                break;
            case error.POSITION_UNAVAILABLE:
                message = 'Location information is unavailable. Check if GPS is turned on.';
                break;
            case error.TIMEOUT:
                message = 'Location request timed out. Try again.';
                break;
            default:
                message = 'Unknown error: ' + error.message;
        }

        statusEl.textContent = 'Tracking paused';
        showFeedback(message, 'error');

        // Stop watching if permission denied
        if (error.code === error.PERMISSION_DENIED && watchId) {
            navigator.geolocation.clearWatch(watchId);
            watchId = null;
            startBtn.classList.remove('hidden');
            stopBtn.classList.add('hidden');
        }
    }

    // ────────────────────────────────────────────────
    // Feedback helper
    // ────────────────────────────────────────────────
    function showFeedback(text, type = 'info') {
        feedback.style.display = 'block';
        feedback.textContent = text;

        if (type === 'success') {
            feedback.className = 'bg-green-50 text-green-800 px-4 py-3 rounded-lg';
        } else if (type === 'error') {
            feedback.className = 'bg-red-50 text-red-800 px-4 py-3 rounded-lg';
        } else {
            feedback.className = 'bg-blue-50 text-blue-800 px-4 py-3 rounded-lg';
        }
    }

    // ────────────────────────────────────────────────
    // Stop Tracking
    // ────────────────────────────────────────────────
    stopBtn.addEventListener('click', () => {
        if (watchId !== null) {
            navigator.geolocation.clearWatch(watchId);
            watchId = null;
        }
        statusEl.textContent = 'Tracking stopped';
        lastUpdateEl.textContent = 'Last sent: Stopped';
        accSpeedEl.textContent = 'Accuracy: — | Speed: —';
        showFeedback('Tracking has been stopped.', 'info');

        startBtn.classList.remove('hidden');
        stopBtn.classList.add('hidden');
    });

    // ────────────────────────────────────────────────
    // Share / Copy Link
    // ────────────────────────────────────────────────
    shareBtn.addEventListener('click', async () => {
        const url = window.location.href;
        if (navigator.share) {
            try {
                await navigator.share({
                    title: 'Live Vehicle Tracking',
                    text: 'Follow my real-time location',
                    url: url
                });
                showFeedback('Shared successfully!', 'success');
            } catch (err) {
                showFeedback('Share canceled or failed.', 'info');
            }
        } else {
            copyToClipboard(url);
        }
    });

    copyBtn.addEventListener('click', () => {
        copyToClipboard(window.location.href);
    });

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            showFeedback('Link copied to clipboard!', 'success');
        }).catch(() => {
            showFeedback('Failed to copy link. Please copy manually.', 'error');
        });
    }
</script>
@endsection