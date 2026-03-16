@extends('layouts.app')

@section('title', 'Request Fuel')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Request Fuel</h1>

    <form method="POST" action="{{ route('driver.fuel-requests.store') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="vehicle_id" class="block text-sm font-medium text-gray-700">Vehicle <span class="text-red-500">*</span></label>
                <select name="vehicle_id" id="vehicle_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select vehicle</option>
                    @foreach ($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">{{ $vehicle->plate_number }} - {{ $vehicle->make }} {{ $vehicle->model }}</option>
                    @endforeach
                </select>
                @error('vehicle_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="requested_amount" class="block text-sm font-medium text-gray-700">Requested Amount (Litres) <span class="text-red-500">*</span></label>
                <input type="number" name="requested_amount" id="requested_amount" step="0.01" min="0.01" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('requested_amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="fuel_type" class="block text-sm font-medium text-gray-700">Fuel Type <span class="text-red-500">*</span></label>
                <select name="fuel_type" id="fuel_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select type</option>
                    <option value="Petrol">Petrol</option>
                    <option value="Diesel">Diesel</option>
                </select>
                @error('fuel_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="odometer_reading" class="block text-sm font-medium text-gray-700">Current Odometer Reading <span class="text-red-500">*</span></label>
                <input type="number" name="odometer_reading" id="odometer_reading" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('odometer_reading') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="odometer_photo" class="block text-sm font-medium text-gray-700">Odometer Photo <span class="text-red-500">*</span></label>
            <input type="file" name="odometer_photo" id="odometer_photo" accept="image/*" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            @error('odometer_photo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="reason" class="block text-sm font-medium text-gray-700">Reason / Notes (optional)</label>
            <textarea name="reason" id="reason" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            @error('reason') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="pt-6">
            <button type="submit" class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-indigo-700 transition shadow">
                Submit Fuel Request
            </button>
        </div>
    </form>
</div>
@endsection