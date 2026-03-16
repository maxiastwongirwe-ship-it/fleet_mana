@extends('layouts.app')

@section('title', 'Complete Fuel Fill-up')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Complete Fuel Fill-up</h1>
    <p class="text-gray-600 mb-8">
        Approved amount: <strong>{{ number_format($fuelRequest->actual_litres_dispensed ?? $fuelRequest->requested_amount, 2) }} L</strong>
    </p>

    <form method="POST" action="{{ route('driver.fuel-requests.store-completion', $fuelRequest) }}" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="station_name" class="block text-sm font-medium text-gray-700">Petrol Station Name <span class="text-red-500">*</span></label>
                <input type="text" name="station_name" id="station_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('station_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="price_per_litre" class="block text-sm font-medium text-gray-700">Price per Litre <span class="text-red-500">*</span></label>
                <input type="number" name="price_per_litre" id="price_per_litre" step="0.01" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('price_per_litre') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="actual_litres_dispensed" class="block text-sm font-medium text-gray-700">Actual Litres Dispensed <span class="text-red-500">*</span></label>
                <input type="number" name="actual_litres_dispensed" id="actual_litres_dispensed" step="0.01" min="0.01" value="{{ old('actual_litres_dispensed', $fuelRequest->actual_litres_dispensed ?? $fuelRequest->requested_amount) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('actual_litres_dispensed') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="total_cost" class="block text-sm font-medium text-gray-700">Total Cost <span class="text-red-500">*</span></label>
                <input type="number" name="total_cost" id="total_cost" step="0.01" min="0.01" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('total_cost') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method <span class="text-red-500">*</span></label>
            <select name="payment_method" id="payment_method" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Select method</option>
                <option value="cash">Cash</option>
                <option value="mobile_money">Mobile Money</option>
                <option value="bank_transfer">Bank Transfer</option>
                <option value="card">Card</option>
            </select>
            @error('payment_method') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Conditional payment fields -->
        <div id="mobile_money_fields" class="hidden space-y-4">
            <label for="promocode" class="block text-sm font-medium text-gray-700">Promo Code / Reference</label>
            <input type="text" name="promocode" id="promocode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div id="bank_transfer_fields" class="hidden space-y-4">
            <label for="bank_account" class="block text-sm font-medium text-gray-700">Bank Account Number</label>
            <input type="text" name="bank_account" id="bank_account" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div id="card_fields" class="hidden space-y-4">
            <label for="card_details" class="block text-sm font-medium text-gray-700">Card Details (last 4 digits or reference)</label>
            <input type="text" name="card_details" id="card_details" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div>
            <label for="receipt_photo" class="block text-sm font-medium text-gray-700">Receipt Photo <span class="text-red-500">*</span></label>
            <input type="file" name="receipt_photo" id="receipt_photo" accept="image/*" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            @error('receipt_photo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="fillup_notes" class="block text-sm font-medium text-gray-700">Additional Notes (optional)</label>
            <textarea name="fillup_notes" id="fillup_notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
        </div>

        <div class="pt-6">
            <button type="submit" class="w-full bg-green-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-green-700 transition shadow">
                Complete Fill-up & Save
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('payment_method').addEventListener('change', function() {
        const mobile = document.getElementById('mobile_money_fields');
        const bank = document.getElementById('bank_transfer_fields');
        const card = document.getElementById('card_fields');

        mobile.classList.add('hidden');
        bank.classList.add('hidden');
        card.classList.add('hidden');

        if (this.value === 'mobile_money') mobile.classList.remove('hidden');
        if (this.value === 'bank_transfer') bank.classList.remove('hidden');
        if (this.value === 'card') card.classList.remove('hidden');
    });
</script>
@endsection