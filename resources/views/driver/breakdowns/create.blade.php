@extends('layouts.driver')

@section('title', 'Report Breakdown')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-4xl font-semibold tracking-tight mb-2">Report Vehicle Breakdown</h1>
    <p class="text-gray-600 mb-8">Please provide full details including estimated repair cost</p>

    <div class="apple-card p-10">
        <form method="POST" action="{{ route('driver.breakdowns.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="space-y-8">
                <!-- Vehicle -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Vehicle</label>
                    <select name="vehicle_id" required class="w-full px-5 py-4 border border-gray-300 rounded-2xl">
                        <option value="">Select the affected vehicle</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->plate_number }} — {{ $vehicle->make }} {{ $vehicle->model }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Location (Optional)</label>
                    <input type="text" name="location" class="w-full px-5 py-4 border border-gray-300 rounded-2xl"
                           placeholder="e.g. Kampala-Jinja Road, near Mukono">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Description of the Problem</label>
                    <textarea name="description" rows="5" required
                              class="w-full px-5 py-4 border border-gray-300 rounded-2xl"
                              placeholder="Describe what happened..."></textarea>
                </div>

                <!-- Estimated Cost -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Estimated Repair Cost (UGX)</label>
                    <input type="number" name="estimated_cost" step="0.01" required
                           class="w-full px-5 py-4 border border-gray-300 rounded-2xl"
                           placeholder="e.g. 450000">
                </div>

                <!-- Date & Time -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">When did it occur?</label>
                    <input type="datetime-local" name="occurred_at" required
                           class="w-full px-5 py-4 border border-gray-300 rounded-2xl">
                </div>

                <!-- Severity -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Severity Level</label>
                    <select name="severity" required class="w-full px-5 py-4 border border-gray-300 rounded-2xl">
                        <option value="minor">Minor</option>
                        <option value="moderate" selected>Moderate</option>
                        <option value="major">Major</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>

                <!-- Photo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Upload Photo (Optional)</label>
                    <input type="file" name="photo" accept="image/*"
                           class="w-full px-5 py-4 border border-gray-300 rounded-2xl">
                </div>
            </div>

            <button type="submit" class="mt-10 w-full py-5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-3xl text-lg transition">
                Submit Breakdown Report
            </button>
        </form>
    </div>
</div>
@endsection