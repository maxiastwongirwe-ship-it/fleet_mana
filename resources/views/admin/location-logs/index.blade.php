@extends('layouts.admin')

@section('title', 'Vehicle Locations')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Vehicle Locations</h1>
                <p class="mt-2 text-gray-600">Real-time GPS tracking overview</p>
            </div>
        </div>

        <!-- Session Messages -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl flex items-center">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-xl flex items-center">
                {{ session('error') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-6 bg-yellow-50 border border-yellow-200 text-yellow-800 px-6 py-4 rounded-xl flex items-center">
                {{ session('warning') }}
            </div>
        @endif

        <!-- Generated Link Display (shows right after generation) -->
        @if (session('tracking_link'))
            <div class="mb-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
                <h3 class="text-xl font-bold mb-4 text-blue-900">New Tracking Link Ready</h3>
                
                <p class="mb-3 text-blue-800">
                    For vehicle: <strong>{{ session('tracking_vehicle') }}</strong>
                </p>

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mb-4">
                    <code class="flex-1 bg-white p-4 rounded-lg border border-blue-100 break-all font-mono text-sm text-gray-800">
                        {{ session('tracking_link') }}
                    </code>

                    <button onclick="navigator.clipboard.writeText('{{ session('tracking_link') }}').then(() => alert('Link copied!'))"
                            class="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition text-sm font-medium whitespace-nowrap">
                        Copy Link
                    </button>
                </div>

                <p class="text-sm italic text-blue-700">
                    This link expires at {{ session('tracking_expires_at') }} (3 minutes from now).
                    Share it immediately with the driver.
                </p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @forelse ($vehicles as $vehicle)
                <div class="bg-white rounded-2xl shadow overflow-hidden">
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold">
                                {{ $vehicle->plate_number }} — {{ $vehicle->make ?? 'N/A' }} {{ $vehicle->model ?? '' }}
                            </h2>
                            @if ($vehicle->latestLocation)
                                <p class="text-sm text-gray-600 mt-1">
                                    Last update: {{ $vehicle->latestLocation->created_at->diffForHumans() }}
                                </p>
                            @else
                                <p class="text-sm text-gray-500 mt-1">No location data yet</p>
                            @endif
                        </div>

                        <!-- Generate link - simple POST form -->
                        <form action="{{ route('admin.vehicles.generate-tracking-link', $vehicle) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                                Generate Link
                            </button>
                        </form>
                    </div>

                    <div class="p-6">
                        @if ($vehicle->latestLocation)
                            <div id="map-{{ $vehicle->id }}" class="h-64 rounded-xl bg-gray-200"></div>

                            <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="font-medium text-gray-700">Coordinates</p>
                                    <p>{{ number_format($vehicle->latestLocation->latitude, 6) }}, {{ number_format($vehicle->latestLocation->longitude, 6) }}</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-700">Accuracy</p>
                                    <p>{{ $vehicle->latestLocation->accuracy ? $vehicle->latestLocation->accuracy . ' m' : 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-700">Speed</p>
                                    <p>{{ $vehicle->latestLocation->speed ? number_format($vehicle->latestLocation->speed * 3.6, 1) . ' km/h' : 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-700">Driver</p>
                                    <p>{{ $vehicle->latestLocation->driver->name ?? 'Unknown' }}</p>
                                </div>
                            </div>

                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    var lat = {{ $vehicle->latestLocation->latitude ?? 0 }};
                                    var lng = {{ $vehicle->latestLocation->longitude ?? 0 }};
                                    if (lat === 0 && lng === 0) return;

                                    var map = L.map('map-{{ $vehicle->id }}').setView([lat, lng], 15);
                                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        attribution: '© OpenStreetMap contributors'
                                    }).addTo(map);

                                    L.marker([lat, lng]).addTo(map)
                                        .bindPopup('Latest position<br>Time: {{ $vehicle->latestLocation->created_at->format('H:i d/m/Y') }}');
                                });
                            </script>
                        @else
                            <div class="h-64 bg-gray-100 rounded-xl flex items-center justify-center">
                                <p class="text-gray-500">No GPS data available for this vehicle yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16 text-gray-500">
                    No vehicles with location data yet.
                </div>
            @endforelse
        </div>
    </div>

    <script>
    function refreshLocations() {
        document.querySelectorAll('[id^="map-"]').forEach(mapEl => {
            const vehicleId = mapEl.id.replace('map-', '');
            fetch(`/admin/vehicles/${vehicleId}/latest-location`)
                .then(res => res.json())
                .then(data => {
                    if (data.latitude && data.longitude) {
                        // You can update marker here if you want live update
                        console.log(`Vehicle ${vehicleId} updated: ${data.latitude}, ${data.longitude}`);
                    }
                });
        });
    }

    // Refresh every 30 seconds
    setInterval(refreshLocations, 30000);
</script>
@endsection