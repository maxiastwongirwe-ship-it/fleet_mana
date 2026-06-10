@extends('layouts.admin')

@section('title', 'Edit Vehicle')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">

    <div class="flex justify-between items-center mb-10">
        <h1 class="text-3xl font-bold text-gray-900">Edit Vehicle</h1>
        <a href="{{ route('admin.vehicles.index') }}" class="text-gray-500 hover:text-gray-700">← Back to Vehicles</a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm p-10">

        {{-- SESSION ERROR (Custom messages like driver already assigned) --}}
        @if (session('error'))
            <div class="bg-red-50 border border-red-300 text-red-700 px-6 py-5 rounded-2xl mb-10">
                <strong class="block mb-3 text-lg">⚠️ Error</strong>
                <p class="text-lg">{{ session('error') }}</p>
            </div>
        @endif

        {{-- VALIDATION ERRORS --}}
        @if ($errors->any())
            <div class="bg-red-50 border border-red-300 text-red-700 px-6 py-5 rounded-2xl mb-10">
                <strong class="block mb-3 text-lg">⚠️ Please correct the following errors:</strong>
                <ul class="list-disc pl-6 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.vehicles.update', $vehicle) }}" 
              enctype="multipart/form-data" class="space-y-8">

            @csrf
            @method('PUT')

            <!-- Current Photo Preview -->
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-3">Current Vehicle Photo</label>
                <div class="w-48 h-48 rounded-2xl overflow-hidden border-2 border-gray-200 bg-gray-100">
                    @if ($vehicle->vehicle_photo_url)
                        <img src="{{ $vehicle->vehicle_photo_url }}" alt="Vehicle" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-6xl text-gray-300">🚛</div>
                    @endif
                </div>
            </div>

            <!-- New Photo Upload -->
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-3">Upload New Photo (optional)</label>
                <input type="file" name="vehicle_photo" accept="image/*"
                       class="block w-full border border-gray-300 rounded-2xl p-4 @error('vehicle_photo') border-red-500 @enderror">
                @error('vehicle_photo')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Plate Number -->
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-3">Plate Number <span class="text-red-500">*</span></label>
                <input type="text" name="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}" required
                       class="w-full border border-gray-300 rounded-2xl p-5 text-lg @error('plate_number') border-red-500 @enderror">
                @error('plate_number')
                    <p class="text-red-500 text-sm mt-2">Invalid format. Examples: UA 123 DL or UAD 238 Q</p>
                @enderror
            </div>

            <!-- Make -->
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-3">Make</label>
                <input type="text" name="make" value="{{ old('make', $vehicle->make) }}"
                       class="w-full border border-gray-300 rounded-2xl p-5 text-lg @error('make') border-red-500 @enderror">
                @error('make')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Model -->
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-3">Model</label>
                <input type="text" name="model" value="{{ old('model', $vehicle->model) }}"
                       class="w-full border border-gray-300 rounded-2xl p-5 text-lg @error('model') border-red-500 @enderror">
                @error('model')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Year -->
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-3">Year</label>
                <input type="number" name="year" value="{{ old('year', $vehicle->year) }}" 
                       min="1900" max="{{ date('Y') }}"
                       class="w-full border border-gray-300 rounded-2xl p-5 text-lg @error('year') border-red-500 @enderror">
                @error('year')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Vehicle Type -->
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-3">Vehicle Type</label>
                <select name="type" 
                        class="w-full border border-gray-300 rounded-2xl p-5 text-lg @error('type') border-red-500 @enderror">
                    <option value="cargo" {{ old('type', $vehicle->type) == 'cargo' ? 'selected' : '' }}>Cargo</option>
                    <option value="passenger" {{ old('type', $vehicle->type) == 'passenger' ? 'selected' : '' }}>Passenger</option>
                </select>
                @error('type')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fuel Type -->
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-3">Fuel Type</label>
                <select name="fuel_type" 
                        class="w-full border border-gray-300 rounded-2xl p-5 text-lg @error('fuel_type') border-red-500 @enderror">
                    <option value="">Select fuel type</option>
                    <option value="Petrol" {{ old('fuel_type', $vehicle->fuel_type) == 'Petrol' ? 'selected' : '' }}>Petrol</option>
                    <option value="Diesel" {{ old('fuel_type', $vehicle->fuel_type) == 'Diesel' ? 'selected' : '' }}>Diesel</option>
                    <option value="Electric" {{ old('fuel_type', $vehicle->fuel_type) == 'Electric' ? 'selected' : '' }}>Electric</option>
                    <option value="Hybrid" {{ old('fuel_type', $vehicle->fuel_type) == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                </select>
                @error('fuel_type')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fuel Tank Capacity -->
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-3">Fuel Tank Capacity (Litres)</label>
                <input type="number" step="0.1" name="fuel_tank_capacity" 
                       value="{{ old('fuel_tank_capacity', $vehicle->fuel_tank_capacity) }}"
                       class="w-full border border-gray-300 rounded-2xl p-5 text-lg @error('fuel_tank_capacity') border-red-500 @enderror">
                @error('fuel_tank_capacity')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Current Odometer -->
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-3">Current Odometer (km)</label>
                <input type="number" name="current_odometer" 
                       value="{{ old('current_odometer', $vehicle->current_odometer) }}" 
                       min="{{ $vehicle->current_odometer ?? 0 }}"
                       class="w-full border border-gray-300 rounded-2xl p-5 text-lg @error('current_odometer') border-red-500 @enderror">
                @error('current_odometer')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Capacity -->
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-3">Capacity</label>
                <input type="number" name="capacity" value="{{ old('capacity', $vehicle->capacity) }}"
                       class="w-full border border-gray-300 rounded-2xl p-5 text-lg @error('capacity') border-red-500 @enderror">
                @error('capacity')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-3">Status</label>
                <select name="status" 
                        class="w-full border border-gray-300 rounded-2xl p-5 text-lg @error('status') border-red-500 @enderror">
                    <option value="active" {{ old('status', $vehicle->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="maintenance" {{ old('status', $vehicle->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="breakdown" {{ old('status', $vehicle->status) == 'breakdown' ? 'selected' : '' }}>Breakdown</option>
                    <option value="retired" {{ old('status', $vehicle->status) == 'retired' ? 'selected' : '' }}>Retired</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Assign Driver -->
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-3">Assign Driver</label>
                <select name="assigned_driver_id" 
                        class="w-full border border-gray-300 rounded-2xl p-5 text-lg @error('assigned_driver_id') border-red-500 @enderror">
                    <option value="">Unassigned</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}" 
                            {{ old('assigned_driver_id', $vehicle->assigned_driver_id) == $driver->id ? 'selected' : '' }}>
                            {{ $driver->name }}
                        </option>
                    @endforeach
                </select>
                @error('assigned_driver_id')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-8 flex justify-end gap-4">
                <a href="{{ route('admin.vehicles.index') }}" 
                   class="px-10 py-5 bg-gray-200 hover:bg-gray-300 rounded-2xl font-medium transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-12 py-5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-2xl transition">
                    Save Changes
                </button>
            </div>

        </form>
    </div>
</div>
@endsection