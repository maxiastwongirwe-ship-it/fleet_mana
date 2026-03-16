@extends('layouts.app')

@section('title', 'Confirm Fuel Intake')

@section('content')
    <div class="max-w-2xl mx-auto p-8 bg-white rounded-2xl shadow">
        <h1 class="text-3xl font-bold mb-8 text-center">Confirm Fuel Intake</h1>

        <div class="bg-gray-50 p-6 rounded-xl mb-8">
            <p class="text-lg mb-2"><strong>Request ID:</strong> {{ $fuelRequest->id }}</p>
            <p class="text-lg mb-2"><strong>Vehicle:</strong> {{ $fuelRequest->vehicle->plate_number }}</p>
            <p class="text-lg mb-4"><strong>Approved Amount:</strong> {{ $fuelRequest->amount_approved }} liters</p>
        </div>

        <form method="POST" action="{{ route('driver.fuel-requests.store-intake', $fuelRequest) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="amount_filled" class="block text-lg font-medium text-gray-700 mb-2">
                    Actual Amount Filled (liters)
                </label>
                <input type="number" name="amount_filled" id="amount_filled" required min="1" step="0.1"
                       class="w-full px-5 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('amount_filled') <p class="mt-1 text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="receipt_photo" class="block text-lg font-medium text-gray-700 mb-2">
                    Upload Receipt Photo (from Shell station)
                </label>
                <input type="file" name="receipt_photo" id="receipt_photo" required accept="image/*"
                       class="w-full px-5 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="mt-2 text-sm text-gray-500">JPG, PNG, max 4MB. Clear photo of receipt required.</p>
                @error('receipt_photo') <p class="mt-1 text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full py-4 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition">
                Confirm Intake & Upload Receipt
            </button>
        </form>
    </div>
@endsection