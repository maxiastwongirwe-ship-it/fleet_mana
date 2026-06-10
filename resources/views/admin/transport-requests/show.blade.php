@extends('layouts.admin')

@section('title', 'Request #' . $transportRequest->id)

@section('content')
<div class="max-w-6xl mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-10">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Transport Request #{{ $transportRequest->id }}</h1>
            <p class="text-gray-500">Requested by {{ $transportRequest->requester->name ?? 'Unknown' }}</p>
        </div>
        <a href="{{ route('admin.transport-requests.index') }}" class="text-indigo-600 hover:text-indigo-700">← Back</a>
    </div>

    <!-- Status -->
    <div class="mb-10">
        <span class="inline-flex px-8 py-3 rounded-3xl text-xl font-semibold
            @if($transportRequest->isPending()) bg-yellow-100 text-yellow-800
            @elseif($transportRequest->isApproved()) bg-blue-100 text-blue-800
            @elseif($transportRequest->isAssigned()) bg-emerald-100 text-emerald-800
            @elseif($transportRequest->isRejected()) bg-red-100 text-red-800
            @else bg-gray-100 text-gray-800 @endif">
            {{ ucfirst($transportRequest->status) }}
        </span>
    </div>

    <!-- Map -->
    <div class="bg-white rounded-3xl shadow-lg p-8 mb-10">
        <h2 class="text-2xl font-semibold mb-6">📍 Route Map</h2>
        <div id="route-map" class="h-[460px] rounded-2xl border border-gray-200 overflow-hidden"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">

            <!-- Request Details -->
            <div class="bg-white rounded-3xl shadow p-8">
                <h2 class="text-2xl font-bold mb-6">Request Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <p class="text-sm text-gray-500">Type</p>
                        <p class="text-xl font-medium">{{ ucfirst($transportRequest->request_type) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Pickup</p>
                        <p class="font-medium">{{ $transportRequest->pickup_location }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Dropoff</p>
                        <p class="font-medium">{{ $transportRequest->dropoff_location }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Pickup Time</p>
                        <p class="font-medium">{{ $transportRequest->pickup_time->format('l, d F Y • g:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- Passengers -->
            @if($transportRequest->passengers->isNotEmpty())
                <div class="bg-white rounded-3xl shadow p-8">
                    <h2 class="text-2xl font-bold mb-6">Passengers</h2>
                    <div class="space-y-4">
                        @foreach($transportRequest->passengers as $p)
                            <div class="bg-gray-50 p-5 rounded-2xl">
                                <p class="font-medium">{{ $p->passenger_name }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Assigned Vehicle Info -->
            @if($transportRequest->trips->isNotEmpty())
                <div class="bg-emerald-50 border border-emerald-200 rounded-3xl p-8">
                    <h2 class="text-2xl font-bold mb-6 text-emerald-800">✅ Vehicle Assigned Successfully</h2>
                    @foreach($transportRequest->trips as $trip)
                        <div class="bg-white p-8 rounded-3xl">
                            <div class="flex justify-between">
                                <div>
                                    <p class="text-3xl font-semibold">{{ $trip->vehicle->plate_number }}</p>
                                    <p class="text-gray-600">{{ $trip->vehicle->make ?? '' }} {{ $trip->vehicle->model ?? '' }}</p>
                                </div>
                                <span class="px-6 py-2 bg-emerald-100 text-emerald-700 rounded-3xl text-sm font-medium">ASSIGNED</span>
                            </div>
                            <div class="mt-6 grid grid-cols-2 gap-8">
                                <div>
                                    <p class="text-sm text-gray-500">Driver</p>
                                    <p class="font-medium">{{ $trip->driver->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Departure Time</p>
                                    <p class="font-medium">{{ $trip->departure_time->format('d M Y • H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Assign Form - Show ONLY if not assigned yet -->
            @if($transportRequest->trips->isEmpty() && in_array($transportRequest->status, ['pending', 'approved']))
                <div class="bg-white rounded-3xl shadow p-8 mt-8">
                    <h3 class="text-2xl font-bold mb-6">Assign Vehicle & Driver</h3>
                    <form method="POST" action="{{ route('admin.transport-requests.assign', $transportRequest) }}">
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Select Vehicle</label>
                                <select name="vehicle_id" required class="w-full px-5 py-4 border border-gray-300 rounded-2xl">
                                    <option value="">-- Choose a vehicle --</option>
                                    @foreach($availableVehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">
                                            {{ $vehicle->plate_number }} — {{ $vehicle->make }} {{ $vehicle->model }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Driver (Optional)</label>
                                <select name="driver_id" class="w-full px-5 py-4 border border-gray-300 rounded-2xl">
                                    <option value="">Use vehicle's assigned driver</option>
                                    @foreach($availableDrivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Departure Time</label>
                                <input type="datetime-local" name="departure_time" required class="w-full px-5 py-4 border border-gray-300 rounded-2xl">
                            </div>
                        </div>

                        <button type="submit" class="mt-10 w-full py-5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-3xl">
                            Assign Vehicle to This Request
                        </button>
                    </form>
                </div>
            @endif

        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl shadow p-8 sticky top-8">
                <h2 class="text-2xl font-bold mb-8">Quick Actions</h2>

                @if($transportRequest->isPending())
                    <form method="POST" action="{{ route('admin.transport-requests.approve', $transportRequest) }}" class="mb-6">
                        @csrf
                        <button type="submit" class="w-full py-4 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-3xl">
                            ✅ Approve Request
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.transport-requests.reject', $transportRequest) }}">
                        @csrf
                        <label class="block text-sm font-medium text-gray-700 mb-3">Rejection Reason</label>
                        <textarea name="admin_notes" rows="4" required class="w-full border border-gray-300 rounded-2xl p-4"></textarea>
                        <button type="submit" class="mt-4 w-full py-4 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-3xl">
                            ❌ Reject Request
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Leaflet -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('route-map').setView([0.3476, 32.5825], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    let bounds = [];

    @if($transportRequest->pickup_lat && $transportRequest->pickup_lng)
        L.marker([{{ $transportRequest->pickup_lat }}, {{ $transportRequest->pickup_lng }}]).addTo(map).bindPopup('Pickup');
        bounds.push([{{ $transportRequest->pickup_lat }}, {{ $transportRequest->pickup_lng }}]);
    @endif

    @if($transportRequest->dropoff_lat && $transportRequest->dropoff_lng)
        L.marker([{{ $transportRequest->dropoff_lat }}, {{ $transportRequest->dropoff_lng }}]).addTo(map).bindPopup('Dropoff');
        bounds.push([{{ $transportRequest->dropoff_lat }}, {{ $transportRequest->dropoff_lng }}]);
    @endif

    if (bounds.length > 0) map.fitBounds(bounds, { padding: [50, 50] });
});
</script>
@endsection