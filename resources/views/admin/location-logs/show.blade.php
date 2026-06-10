@extends('layouts.admin')

@section('title', 'Vehicle History - ' . $vehicle->plate_number)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold">
                {{ $vehicle->plate_number }} — {{ $vehicle->make ?? '' }} {{ $vehicle->model ?? '' }}
            </h1>
            <p class="text-gray-600 mt-1">Full location history</p>
        </div>
        <a href="{{ route('admin.location-logs.index') }}" class="text-indigo-600 hover:text-indigo-800">
            ← Back to Fleet
        </a>
    </div>

    @if ($vehicle->tracking_token)
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 mb-8 flex items-center justify-between">
            <div>
                <p class="font-medium text-emerald-800">Permanent Tracking Link Active</p>
                <code class="text-sm text-emerald-700 break-all">{{ url('/tracking/' . $vehicle->tracking_token) }}</code>
            </div>
            <button onclick="copyLink('{{ url('/tracking/' . $vehicle->tracking_token) }}')" 
                    class="px-5 py-2 bg-emerald-600 text-white rounded-lg text-sm">
                Copy Link
            </button>
        </div>
    @endif

    <!-- Latest Position -->
    @if ($vehicle->latestLocation)
        <div class="bg-white rounded-2xl shadow p-8 mb-10">
            <h2 class="text-2xl font-bold mb-6">Latest Position</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div id="latest-map" class="h-96 rounded-2xl"></div>

                <div class="space-y-6">
                    <div>
                        <p class="text-gray-500">Coordinates</p>
                        <p class="text-2xl font-mono">
                            {{ number_format($vehicle->latestLocation->latitude, 6) }}, 
                            {{ number_format($vehicle->latestLocation->longitude, 6) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500">Last Updated</p>
                        <p class="text-xl">{{ $vehicle->latestLocation->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-gray-500">Speed</p>
                            <p class="text-2xl">{{ $vehicle->latestLocation->speed ? number_format($vehicle->latestLocation->speed * 3.6, 1) : '0' }} km/h</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Accuracy</p>
                            <p class="text-2xl">{{ $vehicle->latestLocation->accuracy ?? '—' }} m</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- History Table -->
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-2xl font-bold">Recent Location Logs</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500">Time</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500">Coordinates</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500">Speed</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500">Accuracy</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500">Driver</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($vehicle->locationLogs()->latest()->take(30)->get() as $log)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">
                                {{ number_format($log->latitude, 6) }}, {{ number_format($log->longitude, 6) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{ $log->speed ? number_format($log->speed * 3.6, 1) . ' km/h' : '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->accuracy ?? '—' }} m</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->driver?->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-gray-500">No location records yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function copyLink(link) {
    navigator.clipboard.writeText(link).then(() => alert('Link copied!'));
}

// Latest Map
@if ($vehicle->latestLocation)
document.addEventListener('DOMContentLoaded', function () {
    var map = L.map('latest-map').setView([
        {{ $vehicle->latestLocation->latitude }},
        {{ $vehicle->latestLocation->longitude }}
    ], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([
        {{ $vehicle->latestLocation->latitude }},
        {{ $vehicle->latestLocation->longitude }}
    ]).addTo(map).bindPopup('Last known position').openPopup();
});
@endif
</script>
@endsection