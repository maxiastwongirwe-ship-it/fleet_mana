@extends('layouts.admin')

@section('title', 'Vehicle: ' . $vehicle->plate_number)

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900">Vehicle Details</h1>
            <div class="flex space-x-6">
                <a href="{{ route('admin.vehicles.edit', $vehicle) }}" 
                   class="px-6 py-3 bg-amber-500 text-white rounded-xl hover:bg-amber-600 transition font-medium">
                    Edit Vehicle
                </a>
                <form action="{{ route('admin.vehicles.destroy', $vehicle) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition font-medium"
                            onclick="return confirm('Delete vehicle {{ $vehicle->plate_number }} permanently?')">
                        Delete Vehicle
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow p-10 space-y-12">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-10">
                <div class="flex-shrink-0">
                    <div class="w-40 h-40 rounded overflow-hidden bg-gray-100 border-4 border-white shadow-lg">
                        @if ($vehicle->vehicle_photo_path)
                            <img src="{{ $vehicle->vehicle_photo_url }}" alt="{{ $vehicle->plate_number }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-6xl font-medium">
                                V
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    <h2 class="text-4xl font-bold text-gray-900">{{ $vehicle->plate_number }}</h2>
                    <p class="text-2xl text-gray-600 mt-2">{{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->year ?? 'N/A' }})</p>
                    <p class="text-xl text-gray-500 mt-1">Type: {{ ucfirst($vehicle->type) }}</p>
                </div>
            </div>

            <!-- Status Badges -->
            <div class="flex flex-wrap gap-4">
                <span class="px-6 py-2 inline-flex text-lg font-medium rounded-full 
                    {{ $vehicle->status === 'active' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                    {{ ucfirst($vehicle->status) }}
                </span>
                <span class="px-6 py-2 inline-flex text-lg font-medium rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                    {{ $vehicle->type_display }} • Capacity: {{ $vehicle->capacity ?? 'N/A' }} {{ $vehicle->type === 'passenger' ? 'seats' : 'kg' }}
                </span>
            </div>

            <!-- Details -->
            <div class="border-t border-gray-200 pt-10">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6">Vehicle Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <p class="text-lg"><strong>Fuel Type:</strong> {{ $vehicle->fuel_type ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-lg"><strong>Tank Capacity:</strong> {{ $vehicle->fuel_tank_capacity ?? 'N/A' }} liters</p>
                    </div>
                    <div>
                        <p class="text-lg"><strong>Current Odometer:</strong> {{ $vehicle->current_odometer }} km</p>
                    </div>
                    <div>
                        <p class="text-lg"><strong>Assigned Driver:</strong> {{ $vehicle->assignedDriver ? $vehicle->assignedDriver->name : 'Unassigned' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection