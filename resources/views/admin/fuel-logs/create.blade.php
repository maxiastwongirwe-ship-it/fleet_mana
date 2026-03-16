@extends('layouts.admin')

@section('title', 'Log Fuel Fill-up')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900">Log Fuel Fill-up</h1>
            <a href="{{ route('admin.fuel-logs.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Logs
            </a>
        </div>

        <form method="POST" action="{{ route('admin.fuel-logs.store') }}" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-lg p-10 space-y-10">
            @csrf

            <!-- Linked Request (optional) -->
            <div>
                <label for="fuel_request_id" class="block text-lg font-medium text-gray-700 mb-3">Linked Approved Request (optional)</label>
                <select name="fuel_request_id" id="fuel_request_id" 
                        class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                    <option value="">-- None / Manual Log --</option>
                    @foreach (\App\Models\FuelRequest::where('status', 'approved')->whereDoesntHave('fuelLog')->get() as $req)
                        <option value="{{ $req->id }}">
                            {{ $req->vehicle->plate_number }} — {{ $req->requested_amount }} L — {{ $req->requester->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Vehicle & Driver -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="vehicle_id" class="block text-lg font-medium text-gray-700 mb-3">Vehicle</label>
                    <select name="vehicle_id" id="vehicle_id" required 
                            class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                        <option value="">-- Select Vehicle --</option>
                        @foreach ($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->plate_number }} — {{ $vehicle->make ?? 'N/A' }} {{ $vehicle->model ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="driver_id" class="block text-lg font-medium text-gray-700 mb-3">Driver</label>
                    <select name="driver_id" id="driver_id" required 
                            class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                        <option value="">-- Select Driver --</option>
                        @foreach ($drivers as $driver)
                            <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                                {{ $driver->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Fuel & Odometer -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="litres_dispensed" class="block text-lg font-medium text-gray-700 mb-3">Litres Dispensed</label>
                    <input type="number" name="litres_dispensed" id="litres_dispensed" value="{{ old('litres_dispensed') }}" step="0.1" min="0.1" required 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                </div>

                <div>
                    <label for="odometer_reading" class="block text-lg font-medium text-gray-700 mb-3">Odometer Reading (km)</label>
                    <input type="number" name="odometer_reading" id="odometer_reading" value="{{ old('odometer_reading') }}" min="0" step="1" required 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                    <p class="mt-2 text-sm text-gray-500">Must be higher than previous log for this vehicle.</p>
                </div>
            </div>

            <!-- Station & Cost -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="station_name" class="block text-lg font-medium text-gray-700 mb-3">Filling Station</label>
                    <input type="text" name="station_name" id="station_name" value="{{ old('station_name') }}" 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                </div>

                <div>
                    <label for="total_cost" class="block text-lg font-medium text-gray-700 mb-3">Total Cost (UGX)</label>
                    <input type="number" name="total_cost" id="total_cost" value="{{ old('total_cost') }}" min="0" step="100" 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                </div>
            </div>

            <!-- Odometer Photo -->
            <div>
                <label for="odometer_photo" class="block text-lg font-medium text-gray-700 mb-3">Odometer Photo (proof)</label>
                <input type="file" name="odometer_photo" id="odometer_photo" accept="image/*" 
                       class="block w-full text-base text-gray-900 file:mr-4 file:py-4 file:px-8 file:rounded-xl file:border-0 file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer cursor-pointer">
                <p class="mt-3 text-sm text-gray-500">Upload clear photo of current odometer reading. Max 5MB.</p>
            </div>

            <!-- Receipt Photo -->
            <div>
                <label for="receipt_photo" class="block text-lg font-medium text-gray-700 mb-3">Receipt / Proof of Payment (optional)</label>
                <input type="file" name="receipt_photo" id="receipt_photo" accept="image/*" 
                       class="block w-full text-base text-gray-900 file:mr-4 file:py-4 file:px-8 file:rounded-xl file:border-0 file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer cursor-pointer">
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-lg font-medium text-gray-700 mb-3">Notes</label>
                <textarea name="notes" id="notes" rows="4" 
                          class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea>
            </div>

            <!-- Submit -->
            <div class="pt-10 flex justify-end">
                <button type="submit" 
                        class="px-12 py-5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium shadow-md">
                    Log Fill-up
                </button>
            </div>
        </form>
    </div>
@endsection