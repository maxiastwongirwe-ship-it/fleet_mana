@extends('layouts.admin')

@section('title', 'Edit Vehicle')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900">Edit Vehicle</h1>
            <a href="{{ route('admin.vehicles.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Vehicles
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-5 rounded-2xl mb-10">
                <p class="font-medium mb-2">Please correct the following:</p>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.vehicles.update', $vehicle) }}" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-lg p-10 space-y-10">
            @csrf
            @method('PUT')

            <!-- Photo -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-10">
                <div class="flex-shrink-0">
                    <label class="block text-lg font-medium text-gray-700 mb-3">Current Photo</label>
                    <div class="w-32 h-32 rounded overflow-hidden bg-gray-100 border-2 border-gray-200">
                        @if ($vehicle->vehicle_photo_path)
                            <img src="{{ Storage::url($vehicle->vehicle_photo_path) }}" alt="{{ $vehicle->plate_number }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-5xl font-medium">
                                V
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex-grow">
                    <label for="vehicle_photo" class="block text-lg font-medium text-gray-700 mb-3">Upload New Photo (optional)</label>
                    <input type="file" name="vehicle_photo" id="vehicle_photo" accept="image/*" 
                           class="block w-full text-base text-gray-900 file:mr-4 file:py-4 file:px-8 file:rounded-xl file:border-0 file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer cursor-pointer">
                    <p class="mt-3 text-sm text-gray-500">Will replace current photo. Max 4MB.</p>
                </div>
            </div>

            <!-- Plate Number -->
            <div>
                <label for="plate_number" class="block text-lg font-medium text-gray-700 mb-3">Plate Number</label>
                <input type="text" name="plate_number" id="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}" required 
                       class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                @error('plate_number') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <!-- Make & Model -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="make" class="block text-lg font-medium text-gray-700 mb-3">Make</label>
                    <input type="text" name="make" id="make" value="{{ old('make', $vehicle->make) }}" 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div>
                    <label for="model" class="block text-lg font-medium text-gray-700 mb-3">Model</label>
                    <input type="text" name="model" id="model" value="{{ old('model', $vehicle->model) }}" 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
            </div>

            <!-- Year -->
            <div>
                <label for="year" class="block text-lg font-medium text-gray-700 mb-3">Year</label>
                <input type="number" name="year" id="year" value="{{ old('year', $vehicle->year) }}" min="1900" max="{{ date('Y') }}" 
                       class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
            </div>

            <!-- Type & Capacity -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="type" class="block text-lg font-medium text-gray-700 mb-3">Vehicle Type</label>
                    <select name="type" id="type" required 
                            class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="cargo" {{ old('type', $vehicle->type) === 'cargo' ? 'selected' : '' }}>Cargo</option>
                        <option value="passenger" {{ old('type', $vehicle->type) === 'passenger' ? 'selected' : '' }}>Passenger</option>
                    </select>
                </div>
                <div>
                    <label for="capacity" class="block text-lg font-medium text-gray-700 mb-3">Capacity</label>
                    <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $vehicle->capacity) }}" min="1" 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <p class="mt-2 text-sm text-gray-500">Seats (passenger) or kg (cargo)</p>
                </div>
            </div>

            <!-- Fuel & Odometer -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <label for="fuel_type" class="block text-lg font-medium text-gray-700 mb-3">Fuel Type</label>
                    <input type="text" name="fuel_type" id="fuel_type" value="{{ old('fuel_type', $vehicle->fuel_type) }}" 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div>
                    <label for="fuel_tank_capacity" class="block text-lg font-medium text-gray-700 mb-3">Fuel Tank Capacity (L)</label>
                    <input type="number" name="fuel_tank_capacity" id="fuel_tank_capacity" value="{{ old('fuel_tank_capacity', $vehicle->fuel_tank_capacity) }}" step="0.1" min="0" 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div>
                    <label for="current_odometer" class="block text-lg font-medium text-gray-700 mb-3">Current Odometer (km)</label>
                    <input type="number" name="current_odometer" id="current_odometer" value="{{ old('current_odometer', $vehicle->current_odometer) }}" min="{{ $vehicle->current_odometer ?? 0 }}" 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <p class="mt-2 text-sm text-gray-500">Cannot be lower than current value</p>
                </div>
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-lg font-medium text-gray-700 mb-3">Vehicle Status</label>
                <select name="status" id="status" required 
                        class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <option value="active" {{ old('status', $vehicle->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="maintenance" {{ old('status', $vehicle->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="breakdown" {{ old('status', $vehicle->status) === 'breakdown' ? 'selected' : '' }}>Breakdown</option>
                    <option value="retired" {{ old('status', $vehicle->status) === 'retired' ? 'selected' : '' }}>Retired</option>
                </select>
            </div>

            <!-- Assigned Driver -->
            <div>
                <label for="assigned_driver_id" class="block text-lg font-medium text-gray-700 mb-3">Assigned Driver</label>
                <select name="assigned_driver_id" id="assigned_driver_id" 
                        class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <option value="">-- Unassigned --</option>
                    @foreach ($drivers as $driver)
                        <option value="{{ $driver->id }}" {{ old('assigned_driver_id', $vehicle->assigned_driver_id) == $driver->id ? 'selected' : '' }}>
                            {{ $driver->name }} ({{ $driver->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Submit -->
            <div class="pt-10 flex justify-end space-x-6">
                <a href="{{ route('admin.vehicles.index') }}" 
                   class="px-10 py-5 bg-gray-200 text-gray-800 rounded-xl hover:bg-gray-300 transition font-medium">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-12 py-5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium shadow-md">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
@endsection