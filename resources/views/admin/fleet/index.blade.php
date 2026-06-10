@extends('layouts.admin')

@section('title', 'Fleet Tracking Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">🚗 Fleet Tracking Dashboard</h1>
            <p class="text-gray-600 mt-1">Generate permanent tracking links for vehicles</p>
        </div>
    </div>

    <!-- Success Message -->
    @if (session('success') || session('tracking_link'))
        <div class="mb-8 bg-emerald-50 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-2xl">
            {{ session('success') }}
        </div>
    @endif

    <!-- Generate Permanent Link -->
    <div class="bg-white rounded-2xl shadow p-8 mb-10">
        <h2 class="text-xl font-semibold mb-6">Generate Tracking Link</h2>
        
        <form method="POST" action="" id="linkForm">
            @csrf

            <div class="max-w-md">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Vehicle</label>
                <select id="vehicleSelect" required 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Choose Vehicle --</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">
                            {{ $vehicle->plate_number }} 
                            — {{ $vehicle->make ?? '' }} {{ $vehicle->model ?? '' }}
                            @if($vehicle->assignedDriver && $vehicle->assignedDriver->user)
                                ({{ $vehicle->assignedDriver->user->name }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" 
                    class="mt-6 px-8 py-3 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition">
                Generate Permanent Link
            </button>
        </form>
    </div>

    <!-- Permanent Link Display -->
    @if(session('tracking_link'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-8 mb-10">
            <h3 class="text-2xl font-bold text-emerald-800 mb-4">✅ Permanent Tracking Link Generated</h3>
            
            <p class="text-lg mb-3">
                <strong>Vehicle:</strong> {{ session('tracking_vehicle') }}
            </p>

            <div class="bg-white p-5 rounded-xl border border-emerald-100 mb-5 break-all font-mono text-sm">
                {{ session('tracking_link') }}
            </div>

            <button onclick="copyLink('{{ session('tracking_link') }}')" 
                    class="px-6 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition">
                📋 Copy Link
            </button>

            <p class="mt-6 text-emerald-700 text-sm">
                Share this link **once** with the driver. The phone will keep sending location automatically 
                whenever it has internet connection.
            </p>
        </div>
    @endif

    <!-- Live Map -->
    <div class="bg-white rounded-2xl shadow p-8">
        <h3 class="text-xl font-semibold mb-6">Live Vehicle Locations</h3>
        <div id="map" class="h-[500px] rounded-2xl"></div>
    </div>

</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    // Form Submit Handler
    document.getElementById('linkForm').addEventListener('submit', function(e) {
        const vehicleId = document.getElementById('vehicleSelect').value;
        
        if (!vehicleId) {
            alert('Please select a vehicle');
            e.preventDefault();
            return;
        }

        this.action = `/admin/vehicles/${vehicleId}/generate-tracking-link`;
    });

    // Copy Link Function
    function copyLink(link) {
        navigator.clipboard.writeText(link).then(() => {
            alert('✅ Link copied to clipboard!');
        }).catch(() => {
            alert('Failed to copy link');
        });
    }

    // Initialize Leaflet Map
    var map = L.map('map').setView([0.3476, 32.5825], 10); // Kampala coordinates

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var markers = [];

    function loadLocations() {
        fetch("/api/fleet-locations")
            .then(res => res.json())
            .then(data => {
                // Clear old markers
                markers.forEach(m => map.removeLayer(m));
                markers = [];

                data.forEach(loc => {
                    let popup = `
                        <b>${loc.plate_number || 'Vehicle'}</b><br>
                        Speed: ${loc.speed ? (loc.speed * 3.6).toFixed(1) : 0} km/h<br>
                        Updated: ${loc.updated_at || 'Just now'}
                    `;

                    let marker = L.marker([loc.latitude, loc.longitude])
                        .addTo(map)
                        .bindPopup(popup);

                    markers.push(marker);
                });
            })
            .catch(err => console.error('Error fetching locations:', err));
    }

    loadLocations();
    setInterval(loadLocations, 8000); // Refresh every 8 seconds
</script>

@endsection