@extends('layouts.admin')

@section('title', 'Fuel Log Details')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-semibold text-gray-900">Fuel Log #{{ $fuelLog->id }}</h1>
            <a href="{{ route('admin.fuel-logs.index') }}" class="text-indigo-600 hover:text-indigo-800">← Back to List</a>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- Basic Info -->
                <div>
                    <h2 class="text-xl font-semibold mb-6">Fill-up Information</h2>
                    <div class="space-y-5">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Vehicle</span>
                            <span class="font-medium">{{ $fuelLog->vehicle->plate_number ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Driver</span>
                            <span class="font-medium">{{ $fuelLog->driver->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Filled At</span>
                            <span class="font-medium">{{ $fuelLog->filled_at->format('d M Y • H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Litres Dispensed</span>
                            <span class="font-semibold text-emerald-600">{{ number_format($fuelLog->litres_dispensed, 2) }} L</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Odometer Reading</span>
                            <span class="font-medium">{{ number_format($fuelLog->odometer_reading) }} km</span>
                        </div>
                    </div>
                </div>

                <!-- Consumption Analysis -->
                <div>
                    <h2 class="text-xl font-semibold mb-6">Consumption Analysis</h2>
                    @if($fuelLog->distance_since_last)
                        <div class="bg-gray-50 rounded-2xl p-6">
                            <div class="flex justify-between mb-4">
                                <span>Distance Travelled</span>
                                <span class="font-semibold">{{ number_format($fuelLog->distance_since_last) }} km</span>
                            </div>
                            <div class="flex justify-between mb-4">
                                <span>Consumption Rate</span>
                                <span class="font-bold text-lg">{{ $fuelLog->litres_per_metre }} L/m</span>
                            </div>
                            <div class="flex justify-between mb-4">
                                <span>Average Last 5</span>
                                <span class="font-medium">
                                    @if($fuelLog->average_previous_litres_per_metre)
                                        {{ number_format($fuelLog->average_previous_litres_per_metre, 7) }} L/m
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span>Km per Litre</span>
                                <span class="font-medium">{{ $fuelLog->km_per_litre }} km/L</span>
                            </div>
                        </div>

                        @if($fuelLog->isSuspicious())
                            <div class="mt-6 bg-red-50 border border-red-200 rounded-2xl p-6 text-red-700">
                                <div class="flex items-center gap-3 text-lg font-semibold">
                                    ⚠️ SUSPICIOUS HIGH CONSUMPTION DETECTED
                                </div>
                                <p class="mt-2">This fill-up shows significantly higher fuel consumption than the vehicle's average over the previous 5 fill-ups.</p>
                                @if($fuelLog->consumption_delta_percent)
                                    <p class="mt-2 text-sm">
                                        Current consumption is {{ number_format($fuelLog->consumption_delta_percent, 2) }}% higher than the 5-log average.
                                    </p>
                                @endif
                            </div>
                        @endif
                            <div class="mt-6 bg-red-50 border border-red-200 rounded-2xl p-6 text-red-700">
                                <div class="flex items-center gap-3 text-lg font-semibold">
                                    ⚠️ SUSPICIOUS HIGH CONSUMPTION DETECTED
                                </div>
                                <p class="mt-2">This fill-up shows significantly higher fuel consumption than the vehicle's normal baseline.</p>
                            </div>
                        @endif
                    @else
                        <p class="text-gray-500">Not enough data to calculate consumption yet.</p>
                    @endif
                </div>
            </div>

            <!-- Photos -->
            <div class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-8">
                @if($fuelLog->odometer_photo_url)
                    <div>
                        <p class="font-medium mb-3">Odometer Photo</p>
                        <img src="{{ $fuelLog->odometer_photo_url }}" 
                             class="w-full rounded-2xl shadow" alt="Odometer">
                    </div>
                @endif
                @if($fuelLog->receipt_photo_url)
                    <div>
                        <p class="font-medium mb-3">Receipt Photo</p>
                        <img src="{{ $fuelLog->receipt_photo_url }}" 
                             class="w-full rounded-2xl shadow" alt="Receipt">
                    </div>
                @endif
            </div>

            <div class="mt-10 pt-8 border-t">
                <a href="{{ route('admin.fuel-logs.index') }}" 
                   class="inline-block px-8 py-4 bg-gray-100 hover:bg-gray-200 rounded-2xl font-medium">
                    Back to All Logs
                </a>
            </div>
        </div>
    </div>
@endsection