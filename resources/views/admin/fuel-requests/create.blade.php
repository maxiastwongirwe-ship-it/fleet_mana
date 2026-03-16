@extends('layouts.admin')

@section('title', 'Create Fuel Request')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-10">
        <h1 class="text-3xl font-semibold text-gray-900 tracking-tight">Create Fuel Request</h1>
        <a href="{{ route('admin.fuel-requests.index') }}"
           class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back
        </a>
    </div>

    <form method="POST" action="{{ route('admin.fuel-requests.store') }}" enctype="multipart/form-data"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 space-y-10">
        @csrf

        <!-- Vehicle -->
        <div>
            <label for="vehicle_id" class="block text-lg font-medium text-gray-700 mb-3">Vehicle *</label>
            <select name="vehicle_id" id="vehicle_id" required
                    class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                <option value="">-- Select Vehicle --</option>
                @foreach ($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                        {{ $vehicle->plate_number }} — {{ $vehicle->make ?? 'N/A' }} {{ $vehicle->model ?? '' }}
                    </option>
                @endforeach
            </select>
            @error('vehicle_id') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Requested Amount & Fuel Type -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <label for="requested_amount" class="block text-lg font-medium text-gray-700 mb-3">Requested Amount (Liters) *</label>
                <input type="number" name="requested_amount" id="requested_amount" value="{{ old('requested_amount') }}" step="0.1" min="0.1" required
                       class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                @error('requested_amount') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="fuel_type" class="block text-lg font-medium text-gray-700 mb-3">Fuel Type *</label>
                <select name="fuel_type" id="fuel_type" required
                        class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                    <option value="">-- Select Type --</option>
                    <option value="Petrol" {{ old('fuel_type') === 'Petrol' ? 'selected' : '' }}>Petrol</option>
                    <option value="Diesel" {{ old('fuel_type') === 'Diesel' ? 'selected' : '' }}>Diesel</option>
                </select>
                @error('fuel_type') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Odometer Reading & Photo -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <label for="odometer_reading" class="block text-lg font-medium text-gray-700 mb-3">Odometer Reading (km)</label>
                <input type="number" name="odometer_reading" id="odometer_reading" value="{{ old('odometer_reading') }}" min="0" step="1"
                       class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                @error('odometer_reading') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="odometer_photo" class="block text-lg font-medium text-gray-700 mb-3">Odometer Photo</label>
                <input type="file" name="odometer_photo" id="odometer_photo" accept="image/*"
                       class="block w-full px-4 py-5 text-lg border border-gray-300 rounded-xl file:mr-6 file:py-4 file:px-8 file:rounded-xl file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                <p class="mt-3 text-sm text-gray-500">Proof of mileage before fill-up. Max 5MB.</p>
                @error('odometer_photo') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Reason / Notes -->
        <div>
            <label for="reason" class="block text-lg font-medium text-gray-700 mb-3">Reason / Purpose</label>
            <textarea name="reason" id="reason" rows="4"
                      class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">{{ old('reason') }}</textarea>
            @error('reason') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Submit -->
        <div class="pt-10 flex justify-end">
            <button type="submit"
                    class="px-12 py-5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium shadow-md">
                Create Request
            </button>
        </div>
    </form>
</div>
@endsection