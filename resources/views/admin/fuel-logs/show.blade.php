@extends('layouts.admin')

@section('title', 'Fuel Log Details')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900">Fuel Fill-up Details</h1>
            <a href="{{ route('admin.fuel-logs.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Logs
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow p-10 space-y-12">
            <!-- Main Info -->
            <div class="border-b border-gray-200 pb-8">
                <h3 class="text-2xl font-semibold text-gray-900 mb-4">Fill-up Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <p class="text-lg"><strong>Vehicle:</strong> {{ $fuelLog->vehicle->plate_number }}</p>
                    </div>
                    <div>
                        <p class="text-lg"><strong>Driver:</strong> {{ $fuelLog->driver ? $fuelLog->driver->name : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-lg"><strong>Litres Dispensed:</strong> {{ number_format($fuelLog->litres_dispensed, 2) }} L</p>
                    </div>
                    <div>
                        <p class="text-lg"><strong>Odometer Reading:</strong> {{ $fuelLog->odometer_reading }} km</p>
                    </div>
                    <div>
                        <p class="text-lg"><strong>Filled At:</strong> {{ $fuelLog->filled_at->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-lg"><strong>Station:</strong> {{ $fuelLog->station_name ?? 'Not specified' }}</p>
                    </div>
                </div>
            </div>

            <!-- Distance & Previous -->
            <div class="border-b border-gray-200 pb-8">
                <h3 class="text-2xl font-semibold text-gray-900 mb-4">Distance & Fuel Consumption</h3>
                @if ($fuelLog->distanceSinceLast)
                    <p class="text-lg">Distance since last fill-up: <strong>{{ $fuelLog->distanceSinceLast }} km</strong></p>
                @else
                    <p class="text-lg text-gray-600">This is the first logged fill-up for this vehicle.</p>
                @endif

                @if ($fuelLog->previousLitres)
                    <p class="text-lg mt-2">Previous fill-up amount: <strong>{{ number_format($fuelLog->previousLitres, 2) }} L</strong></p>
                @endif
            </div>

            <!-- Photos -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Odometer Photo</h3>
                    @if ($fuelLog->odometerPhotoUrl)
                        <a href="{{ $fuelLog->odometerPhotoUrl }}" target="_blank">
                            <img src="{{ $fuelLog->odometerPhotoUrl }}" alt="Odometer" class="w-full max-h-80 object-contain rounded-xl shadow">
                        </a>
                    @else
                        <p class="text-gray-600">No odometer photo.</p>
                    @endif
                </div>

                <div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Receipt / Proof</h3>
                    @if ($fuelLog->receiptPhotoUrl)
                        <a href="{{ $fuelLog->receiptPhotoUrl }}" target="_blank">
                            <img src="{{ $fuelLog->receiptPhotoUrl }}" alt="Receipt" class="w-full max-h-80 object-contain rounded-xl shadow">
                        </a>
                    @else
                        <p class="text-gray-600">No receipt photo.</p>
                    @endif
                </div>
            </div>

            <!-- Previous 5 Logs -->
            <div class="border-t border-gray-200 pt-10">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6">Previous 5 Fill-ups for this Vehicle</h3>
                @if ($previousLogs->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Litres</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Odometer</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Distance</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($previousLogs as $prev)
                                    <tr>
                                        <td class="px-6 py-4">{{ $prev->filled_at->format('d M Y H:i') }}</td>
                                        <td class="px-6 py-4">{{ number_format($prev->litres_dispensed, 2) }} L</td>
                                        <td class="px-6 py-4">{{ $prev->odometer_reading }} km</td>
                                        <td class="px-6 py-4">
                                            @if ($prev->previousLog)
                                                {{ $prev->odometer_reading - $prev->previousLog->odometer_reading }} km
                                            @else
                                                First
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-600">No previous fill-ups for this vehicle.</p>
                @endif
            </div>
        </div>
    </div>
@endsection