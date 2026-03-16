@extends('layouts.app')

@section('title', 'Request Fuel')

@section('content')
    <div class="max-w-2xl mx-auto p-8 bg-white rounded-2xl shadow">
        <h1 class="text-3xl font-bold mb-8 text-center">Request Fuel</h1>

        <form method="POST" action="{{ route('driver.fuel-requests.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="vehicle_id" class="block text-lg font-medium text-gray-700 mb-2">
                    Select Vehicle
                </label>
                <select id="vehicle_id" name="vehicle_id" required 
                        class="w-full px-5 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Choose your vehicle --</option>
                    @foreach ($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">
                            {{ $vehicle->plate_number }} - {{ $vehicle->make }} {{ $vehicle->model }}
                        </option>
                    @endforeach
                </select>
                @error('vehicle_id') <p class="mt-1 text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="amount_requested" class="block text-lg font-medium text-gray-700 mb-2">
                    Amount Requested (liters)
                </label>
                <input type="number" name="amount_requested" id="amount_requested" required min="1" step="0.1"
                       class="w-full px-5 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('amount_requested') <p class="mt-1 text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="reason" class="block text-lg font-medium text-gray-700 mb-2">
                    Reason (optional)
                </label>
                <textarea name="reason" id="reason" rows="4"
                          class="w-full px-5 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                @error('reason') <p class="mt-1 text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full py-4 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition">
                Submit Fuel Request
            </button>
        </form>
    </div>
@endsection