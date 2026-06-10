@extends('layouts.admin')

@section('title', 'Log New Fuel Fill-up')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-semibold text-gray-900">Log New Fuel Fill-up</h1>
                <p class="text-gray-500">Record actual fuel dispensed and monitor consumption</p>
            </div>
            <a href="{{ route('admin.fuel-logs.index') }}" 
               class="text-gray-500 hover:text-gray-700 flex items-center gap-2">
                ← Back to Fuel Activity
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-10">
            <form method="POST" action="{{ route('admin.fuel-logs.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Link to Approved Request -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Link to Approved Request (Optional)</label>
                    <select name="fuel_request_id" 
                            class="w-full px-6 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">New Manual Entry</option>
                        @foreach($approvedRequests as $req)
                            <option value="{{ $req->id }}">
                                {{ $req->vehicle->plate_number }} — {{ $req->requested_amount }} L requested
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Vehicle -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Vehicle <span class="text-red-500">*</span></label>
                        <select name="vehicle_id" id="vehicleSelect" required
                                class="w-full px-6 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select Vehicle</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" data-logs='@json($vehicle->fuelLogs)'>
                                    {{ $vehicle->plate_number }} — {{ $vehicle->make }} {{ $vehicle->model }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Driver -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Driver <span class="text-red-500">*</span></label>
                        <select name="driver_id" required
                                class="w-full px-6 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select Driver</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Consumption History Preview -->
                <div id="consumptionPreview" class="hidden mt-8 bg-blue-50 border border-blue-200 rounded-2xl p-6">
                    <h3 class="font-semibold text-blue-900 mb-4">📊 Consumption History (Last 5 Fill-ups)</h3>
                    <div id="consumptionTable" class="space-y-3 text-sm text-blue-800">
                        <p>Loading...</p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-blue-200">
                        <div class="flex justify-between font-semibold text-blue-900">
                            <span>Average Consumption (L/m):</span>
                            <span id="avgConsumption">—</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Litres Dispensed <span class="text-red-500">*</span></label>
                        <input type="number" name="litres_dispensed" step="0.01" required
                               class="w-full px-6 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500"
                               placeholder="e.g. 45.5">
                    </div>

                    <!-- Odometer Reading -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Current Odometer (km) <span class="text-red-500">*</span></label>
                        <input type="number" name="odometer_reading" required
                               class="w-full px-6 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500"
                               placeholder="Current odometer reading">
                    </div>

                    <!-- Fuel Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Fuel Type</label>
                        <input type="text" name="fuel_type" value="{{ old('fuel_type') }}"
                               class="w-full px-6 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Station Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Station Name</label>
                        <input type="text" name="station_name"
                               class="w-full px-6 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Total Cost -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Total Cost ($)</label>
                        <input type="number" name="total_cost" step="0.01"
                               class="w-full px-6 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Notes -->
                <div class="mt-8">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Notes / Remarks</label>
                    <textarea name="notes" rows="4"
                              class="w-full px-6 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <!-- Photos -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Odometer Photo</label>
                        <input type="file" name="odometer_photo" accept="image/*"
                               class="w-full px-6 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Receipt Photo</label>
                        <input type="file" name="receipt_photo" accept="image/*"
                               class="w-full px-6 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="mt-10">
                    <button type="submit"
                            class="w-full py-5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-lg rounded-2xl transition">
                        Save Fuel Fill-up Record
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const approvedRequests = @json($approvedRequests);
        const requestSelect = document.querySelector('select[name="fuel_request_id"]');
        const vehicleSelect = document.getElementById('vehicleSelect');
        const driverSelect = document.querySelector('select[name="driver_id"]');
        const fuelTypeInput = document.querySelector('input[name="fuel_type"]');
        const stationInput = document.querySelector('input[name="station_name"]');
        const totalCostInput = document.querySelector('input[name="total_cost"]');
        const odometerInput = document.querySelector('input[name="odometer_reading"]');

        function displayConsumptionHistory() {
            const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex];
            const logsJson = selectedOption.getAttribute('data-logs');
            
            if (!logsJson || logsJson === '' || logsJson === '[]') {
                document.getElementById('consumptionPreview').classList.add('hidden');
                return;
            }

            const logs = JSON.parse(logsJson);
            const preview = document.getElementById('consumptionPreview');
            const table = document.getElementById('consumptionTable');
            const avgSpan = document.getElementById('avgConsumption');

            if (logs.length === 0) {
                preview.classList.add('hidden');
                return;
            }

            let html = '';
            let totalConsumption = 0;
            let validLogs = 0;

            for (let i = 0; i < logs.length; i++) {
                const log = logs[i];
                const distanceSinceLast = i > 0 ? (log.odometer_reading - logs[i-1].odometer_reading) : null;
                const litresPerM = distanceSinceLast ? ((log.litres_dispensed / (distanceSinceLast * 1000)).toFixed(7)) : null;
                const kmPerL = distanceSinceLast ? ((distanceSinceLast / log.litres_dispensed).toFixed(2)) : null;

                if (litresPerM) {
                    totalConsumption += parseFloat(litresPerM);
                    validLogs++;
                }

                html += `
                    <div class="flex justify-between p-2 bg-white rounded">
                        <span>${log.filled_at.split(' ')[0]} · ${log.litres_dispensed}L</span>
                        <span>${litresPerM ? litresPerM + ' L/m' : 'N/A'}</span>
                    </div>
                `;
            }

            table.innerHTML = html;
            const avgValue = validLogs > 0 ? (totalConsumption / validLogs).toFixed(7) : 'N/A';
            avgSpan.textContent = avgValue;

            if (validLogs > 0) {
                preview.classList.remove('hidden');
            }
        }

        function updateFuelLogFields() {
            const selectedId = requestSelect.value;
            if (!selectedId) {
                vehicleSelect.value = '';
                driverSelect.value = '';
                document.getElementById('consumptionPreview').classList.add('hidden');
                return;
            }

            const selectedRequest = approvedRequests.find(req => req.id.toString() === selectedId.toString());
            if (!selectedRequest) return;

            if (selectedRequest.vehicle) {
                vehicleSelect.value = selectedRequest.vehicle.id;
                displayConsumptionHistory();
                
                if (selectedRequest.vehicle.assigned_driver_id) {
                    driverSelect.value = selectedRequest.vehicle.assigned_driver_id;
                }
            }

            if (selectedRequest.fuel_type && !fuelTypeInput.value) {
                fuelTypeInput.value = selectedRequest.fuel_type;
            }
            if (selectedRequest.station_name && !stationInput.value) {
                stationInput.value = selectedRequest.station_name;
            }
            if (selectedRequest.total_cost && !totalCostInput.value) {
                totalCostInput.value = selectedRequest.total_cost;
            }
            if (selectedRequest.odometer_reading && !odometerInput.value) {
                odometerInput.value = selectedRequest.odometer_reading;
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            requestSelect.addEventListener('change', updateFuelLogFields);
            vehicleSelect.addEventListener('change', displayConsumptionHistory);
        });
    </script>
@endsection