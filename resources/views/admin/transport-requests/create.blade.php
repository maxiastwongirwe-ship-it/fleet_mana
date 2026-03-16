@extends('layouts.admin')

@section('title', 'New Transport Request')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-10">
            <h1 class="text-3xl font-bold text-gray-900">Create New Transport Request</h1>
            <p class="mt-2 text-gray-600">Passenger or goods transport request</p>
        </div>

        <form method="POST" action="{{ route('admin.transport-requests.store') }}" class="bg-white rounded-2xl shadow p-8 space-y-8">
            @csrf

            <!-- Request Type -->
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-3">Request Type</label>
                <div class="flex gap-6">
                    <label class="inline-flex items-center">
                        <input type="radio" name="request_type" value="passenger" checked required class="h-5 w-5 text-indigo-600">
                        <span class="ml-3 text-lg">Passenger / Worker Transport</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="request_type" value="goods" required class="h-5 w-5 text-indigo-600">
                        <span class="ml-3 text-lg">Goods / Cargo Transport</span>
                    </label>
                </div>
            </div>

            <!-- Requested By -->
            <div>
                <label for="requested_by" class="block text-lg font-medium text-gray-700 mb-3">Requested By</label>
                <select name="requested_by" id="requested_by" required
                        class="block w-full px-5 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select requester</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} — {{ $user->email }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Locations -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="pickup_location" class="block text-lg font-medium text-gray-700 mb-3">Pickup Location</label>
                    <input type="text" name="pickup_location" id="pickup_location" required
                           class="block w-full px-5 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="e.g. Company HQ, Kampala">
                </div>
                <div>
                    <label for="dropoff_location" class="block text-lg font-medium text-gray-700 mb-3">Drop-off Location</label>
                    <input type="text" name="dropoff_location" id="dropoff_location" required
                           class="block w-full px-5 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="e.g. Jinja Industrial Area">
                </div>
            </div>

            <!-- Pickup Time -->
            <div>
                <label for="pickup_time" class="block text-lg font-medium text-gray-700 mb-3">Pickup Time</label>
                <input type="datetime-local" name="pickup_time" id="pickup_time" required
                       class="block w-full px-5 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Purpose / Notes -->
            <div>
                <label for="purpose" class="block text-lg font-medium text-gray-700 mb-3">Purpose / Additional Notes</label>
                <textarea name="purpose" id="purpose" rows="4"
                          class="block w-full px-5 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="e.g. Transport 12 workers to training, or deliver 500kg of spare parts"></textarea>
            </div>

            <!-- Passenger list (shown only for passenger type) -->
            <div id="passenger-section" class="hidden">
                <label class="block text-lg font-medium text-gray-700 mb-4">Passengers</label>
                <div id="passenger-container" class="space-y-4">
                    <!-- Dynamic passengers will be added here via JS -->
                </div>
                <button type="button" id="add-passenger"
                        class="mt-4 inline-flex items-center px-5 py-3 bg-gray-100 text-gray-800 rounded-xl hover:bg-gray-200 transition">
                    + Add Passenger
                </button>
            </div>

            <!-- Submit -->
            <div class="pt-8 flex justify-end">
                <button type="submit"
                        class="px-10 py-4 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition shadow-md">
                    Submit Request
                </button>
            </div>
        </form>
    </div>

    <!-- Simple JS for dynamic passengers -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const typeRadios = document.querySelectorAll('input[name="request_type"]');
            const passengerSection = document.getElementById('passenger-section');
            const container = document.getElementById('passenger-container');
            const addBtn = document.getElementById('add-passenger');

            function togglePassengerSection() {
                const selected = document.querySelector('input[name="request_type"]:checked').value;
                passengerSection.classList.toggle('hidden', selected !== 'passenger');
            }

            typeRadios.forEach(radio => {
                radio.addEventListener('change', togglePassengerSection);
            });

            togglePassengerSection(); // initial check

            let passengerCount = 0;

            addBtn.addEventListener('click', () => {
                passengerCount++;
                const div = document.createElement('div');
                div.className = 'flex gap-4 items-end';
                div.innerHTML = `
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Passenger Name</label>
                        <input type="text" name="passengers[${passengerCount}][name]" required
                               class="block w-full px-4 py-3 border border-gray-300 rounded-xl">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Linked User (optional)</label>
                        <select name="passengers[${passengerCount}][user_id]"
                                class="block w-full px-4 py-3 border border-gray-300 rounded-xl">
                            <option value="">None</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" class="mb-2 text-red-600 hover:text-red-800"
                            onclick="this.parentElement.remove()">
                        Remove
                    </button>
                `;
                container.appendChild(div);
            });
        });
    </script>
@endsection