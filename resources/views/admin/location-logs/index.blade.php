@extends('layouts.admin')

@section('title', 'Fleet Vehicle Tracking')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Fleet Vehicles Tracking</h1>
            <p class="text-gray-600">Select a vehicle to generate permanent tracking link</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <!-- Permanent Link Display -->
    @if (session('tracking_link'))
        <div class="mb-8 bg-emerald-50 border border-emerald-200 rounded-2xl p-6">
            <h3 class="text-xl font-bold text-emerald-900 mb-3">✅ Permanent Tracking Link Ready</h3>
            <p class="mb-3">
                Vehicle: <strong>{{ session('tracking_vehicle') }}</strong>
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 bg-white p-4 rounded-xl border">
                <code class="flex-1 p-4 bg-gray-50 rounded-lg font-mono text-sm break-all">
                    {{ session('tracking_link') }}
                </code>
                <button onclick="copyToClipboard('{{ session('tracking_link') }}')" 
                        class="px-8 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700">
                    Copy Link
                </button>
            </div>

            <p class="text-sm text-emerald-700 mt-4">
                Share this link once with the driver. The phone will continue sending location 
                anytime it has internet connection.
            </p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($vehicles as $vehicle)
            <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between">
                        <h2 class="text-2xl font-bold">{{ $vehicle->plate_number }}</h2>
                        @if ($vehicle->tracking_token)
                            <span class="px-4 py-1 text-xs font-semibold bg-emerald-100 text-emerald-700 rounded-full">Active</span>
                        @else
                            <span class="px-4 py-1 text-xs font-semibold bg-gray-100 text-gray-600 rounded-full">Inactive</span>
                        @endif
                    </div>
                    <p class="text-gray-500">{{ $vehicle->make ?? '' }} {{ $vehicle->model ?? '' }}</p>
                </div>

                <div class="px-6 pb-6">
                    @if ($vehicle->latestLocation)
                        <div id="map-{{ $vehicle->id }}" class="h-52 rounded-xl"></div>
                        <div class="mt-4 text-sm grid grid-cols-2 gap-3">
                            <div>
                                <span class="text-gray-500">Last Update:</span><br>
                                <strong>{{ $vehicle->latestLocation->created_at->diffForHumans() }}</strong>
                            </div>
                            <div>
                                <span class="text-gray-500">Speed:</span><br>
                                <strong>{{ $vehicle->latestLocation->speed ? number_format($vehicle->latestLocation->speed * 3.6, 1) : '0' }} km/h</strong>
                            </div>
                        </div>
                    @else
                        <div class="h-52 bg-gray-100 rounded-xl flex items-center justify-center">
                            <p class="text-gray-500 text-center">No location data yet</p>
                        </div>
                    @endif
                </div>

                <div class="border-t p-6 flex gap-3 bg-gray-50">
                    @if (!$vehicle->tracking_token)
                        <form action="{{ route('admin.vehicles.generate-tracking-link', $vehicle) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium">
                                Generate Permanent Link
                            </button>
                        </form>
                    @else
                        <a href="{{ url('/tracking/' . $vehicle->tracking_token) }}" target="_blank"
                           class="flex-1 py-3.5 text-center border border-gray-300 hover:bg-gray-100 rounded-xl text-sm font-medium">
                            Open Tracking Page
                        </a>
                    @endif

                    <a href="{{ route('admin.vehicles.show', $vehicle) }}" 
                       class="flex-1 py-3.5 text-center bg-gray-800 text-white rounded-xl text-sm font-medium hover:bg-gray-900">
                        View History
                    </a>
                </div>
            </div>
        @empty
            <p class="col-span-full text-center py-10 text-gray-500">No vehicles registered yet.</p>
        @endforelse
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert("✅ Link copied to clipboard!");
    });
}

// Optional: Auto refresh every 25 seconds
setInterval(() => location.reload(), 25000);
</script>
@endsection