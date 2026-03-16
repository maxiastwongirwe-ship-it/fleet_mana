@extends('layouts.admin')

@section('title', 'Vehicle Location History - ' . $vehicle->plate_number)

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ $vehicle->plate_number }} — {{ $vehicle->make ?? 'N/A' }} {{ $vehicle->model ?? '' }}
                </h1>
                <p class="mt-2 text-gray-600">Location tracking history</p>
            </div>
            <a href="{{ route('admin.location-logs.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                ← Back to overview
            </a>
        </div>

        <!-- Latest position card -->
        @if ($vehicle->latestLocation)
            <div class="bg-white rounded-2xl shadow p-8 mb-8">
                <h2 class="text-2xl font-bold mb-6">Latest Known Position</h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div id="latest-map" class="h-96 rounded-xl"></div>

                    <div class="space-y-6">
                        <div>
                            <p class="text-lg font-medium text-gray-700">Coordinates</p>
                            <p class="text-2xl mt-1">
                                {{ number_format($vehicle->latestLocation->latitude, 6) }}, 
                                {{ number_format($vehicle->latestLocation->longitude, 6) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-lg font-medium text-gray-700">Last Updated</p>
                            <p class="text-xl mt-1">{{ $vehicle->latestLocation->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-lg font-medium text-gray-700">Accuracy</p>
                                <p class="text-xl mt-1">{{ $vehicle->latestLocation->accuracy ? $vehicle->latestLocation->accuracy . ' m' : 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-lg font-medium text-gray-700">Speed</p>
                                <p class="text-xl mt-1">
                                    {{ $vehicle->latestLocation->speed ? number_format($vehicle->latestLocation->speed * 3.6, 1) . ' km/h' : 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <p class="text-lg font-medium text-gray-700">Driver</p>
                            <p class="text-xl mt-1">{{ $vehicle->latestLocation->driver->name ?? 'Unknown' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var map = L.map('latest-map').setView([
                        {{ $vehicle->latestLocation->latitude }},
                        {{ $vehicle->latestLocation->longitude }}
                    ], 16);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    L.marker([
                        {{ $vehicle->latestLocation->latitude }},
                        {{ $vehicle->latestLocation->longitude }}
                    ]).addTo(map)
                        .bindPopup(`
                            <b>Latest position</b><br>
                            Time: {{ $vehicle->latestLocation->created_at->format('d M Y H:i') }}<br>
                            Driver: {{ $vehicle->latestLocation->driver->name ?? 'Unknown' }}
                        `).openPopup();
                });
            </script>
        @else
            <div class="bg-white rounded-2xl shadow p-12 text-center">
                <p class="text-xl text-gray-600">No location data recorded for this vehicle yet.</p>
            </div>
        @endif

        <!-- Recent logs table -->
        <div class="bg-white rounded-2xl shadow overflow-hidden mt-8">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold">Recent Location Logs</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Time</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Coordinates</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Accuracy</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Speed</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Driver</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($vehicle->locationLogs()->latest()->take(20)->get() as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $log->created_at->format('d M Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ number_format($log->latitude, 6) }}, {{ number_format($log->longitude, 6) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $log->accuracy ? $log->accuracy . ' m' : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $log->speed ? number_format($log->speed * 3.6, 1) . ' km/h' : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $log->driver->name ?? 'Unknown' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No location logs recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection