@extends('layouts.admin')

@section('title', 'Edit Fuel Request')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-10">
        <h1 class="text-3xl font-semibold text-gray-900 tracking-tight">Edit Fuel Request</h1>
        <a href="{{ route('admin.fuel-requests.index') }}"
           class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-8 py-6 rounded-2xl mb-10">
            <p class="font-medium mb-3">Please fix the following errors:</p>
            <ul class="list-disc pl-6 space-y-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.fuel-requests.update', $fuelRequest) }}" enctype="multipart/form-data"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 space-y-10">
        @csrf @method('PUT')

        <!-- Vehicle -->
        <div>
            <label for="vehicle_id" class="block text-lg font-medium text-gray-700 mb-3">Vehicle *</label>
            <select name="vehicle_id" id="vehicle_id" required
                    class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                @foreach ($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $fuelRequest->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
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
                <input type="number" name="requested_amount" id="requested_amount"
                       value="{{ old('requested_amount', $fuelRequest->requested_amount) }}" step="0.1" min="0.1" required
                       class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                @error('requested_amount') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="fuel_type" class="block text-lg font-medium text-gray-700 mb-3">Fuel Type *</label>
                <select name="fuel_type" id="fuel_type" required
                        class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                    <option value="Petrol" {{ old('fuel_type', $fuelRequest->fuel_type) === 'Petrol' ? 'selected' : '' }}>Petrol</option>
                    <option value="Diesel" {{ old('fuel_type', $fuelRequest->fuel_type) === 'Diesel' ? 'selected' : '' }}>Diesel</option>
                </select>
                @error('fuel_type') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Odometer Reading -->
        <div>
            <label for="odometer_reading" class="block text-lg font-medium text-gray-700 mb-3">Odometer Reading (km)</label>
            <input type="number" name="odometer_reading" id="odometer_reading"
                   value="{{ old('odometer_reading', $fuelRequest->odometer_reading) }}" min="0" step="1"
                   class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
            @error('odometer_reading') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Odometer Photo -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-3">Current Odometer Photo</label>
                @if ($fuelRequest->odometer_photo_url)
                    <a href="{{ $fuelRequest->odometer_photo_url }}" target="_blank" class="block mb-4">
                        <img src="{{ $fuelRequest->odometer_photo_url }}" alt="Odometer"
                             class="w-full max-h-64 object-contain rounded-xl shadow hover:scale-105 transition">
                    </a>
                @else
                    <p class="text-gray-600 italic">No odometer photo uploaded yet.</p>
                @endif
            </div>

            <div>
                <label for="odometer_photo" class="block text-lg font-medium text-gray-700 mb-3">Replace Photo (optional)</label>
                <input type="file" name="odometer_photo" accept="image/*"
                       class="block w-full px-4 py-5 text-lg border border-gray-300 rounded-xl file:mr-6 file:py-4 file:px-8 file:rounded-xl file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                @error('odometer_photo') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Actual Litres & Status -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <label for="actual_litres_dispensed" class="block text-lg font-medium text-gray-700 mb-3">Actual Litres Dispensed</label>
                <input type="number" name="actual_litres_dispensed" id="actual_litres_dispensed"
                       value="{{ old('actual_litres_dispensed', $fuelRequest->actual_litres_dispensed) }}" step="0.1" min="0"
                       class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                @error('actual_litres_dispensed') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="status" class="block text-lg font-medium text-gray-700 mb-3">Status *</label>
                <select name="status" id="status" required
                        class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                    <option value="requested" {{ old('status', $fuelRequest->status) === 'requested' ? 'selected' : '' }}>Requested</option>
                    <option value="pending" {{ old('status', $fuelRequest->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ old('status', $fuelRequest->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ old('status', $fuelRequest->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="completed" {{ old('status', $fuelRequest->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                @error('status') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Station, Cost, Payment -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <label for="station_name" class="block text-lg font-medium text-gray-700 mb-3">Station Name</label>
                <input type="text" name="station_name" id="station_name" value="{{ old('station_name', $fuelRequest->station_name) }}"
                       class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                @error('station_name') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="total_cost" class="block text-lg font-medium text-gray-700 mb-3">Total Cost (UGX)</label>
                <input type="number" name="total_cost" id="total_cost" value="{{ old('total_cost', $fuelRequest->total_cost) }}" min="0"
                       class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                @error('total_cost') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="payment_method" class="block text-lg font-medium text-gray-700 mb-3">Payment Method</label>
                <input type="text" name="payment_method" id="payment_method" value="{{ old('payment_method', $fuelRequest->payment_method) }}"
                       class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                @error('payment_method') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Reason / Notes -->
        <div>
            <label for="reason" class="block text-lg font-medium text-gray-700 mb-3">Reason / Purpose</label>
            <textarea name="reason" id="reason" rows="4"
                      class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">{{ old('reason', $fuelRequest->reason) }}</textarea>
            @error('reason') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Admin Notes (visible on edit) -->
        <div>
            <label for="admin_notes" class="block text-lg font-medium text-gray-700 mb-3">Admin Notes</label>
            <textarea name="admin_notes" id="admin_notes" rows="4"
                      class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">{{ old('admin_notes', $fuelRequest->admin_notes) }}</textarea>
            @error('admin_notes') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Submit -->
        <div class="pt-12 flex justify-end gap-6">
            <a href="{{ route('admin.fuel-requests.index') }}"
               class="px-10 py-5 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition">
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