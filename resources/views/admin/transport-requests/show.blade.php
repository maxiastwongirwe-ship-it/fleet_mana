@extends('layouts.admin')

@section('title', 'Transport Request #' . $transportRequest->id)

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
            <h1 class="text-3xl font-bold text-gray-900">
                Transport Request #{{ $transportRequest->id }}
            </h1>
            <a href="{{ route('admin.transport-requests.index') }}"
               class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                ← Back to list
            </a>
        </div>

        <!-- Status banner -->
        <div class="mb-8">
            <span class="inline-flex px-6 py-3 rounded-full text-xl font-semibold
                {{ match ($transportRequest->status) {
                    'pending'   => 'bg-yellow-100 text-yellow-800 border-2 border-yellow-200',
                    'approved'  => 'bg-blue-100 text-blue-800 border-2 border-blue-200',
                    'rejected'  => 'bg-red-100 text-red-800 border-2 border-red-200',
                    'grouped'   => 'bg-purple-100 text-purple-800 border-2 border-purple-200',
                    'assigned'  => 'bg-indigo-100 text-indigo-800 border-2 border-indigo-200',
                    'completed' => 'bg-green-100 text-green-800 border-2 border-green-200',
                } }}">
                {{ ucfirst($transportRequest->status) }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-2xl shadow p-8">
                    <h2 class="text-2xl font-bold mb-6">Request Details</h2>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Type</dt>
                            <dd class="mt-1 text-lg font-medium">{{ ucfirst($transportRequest->request_type) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Requested by</dt>
                            <dd class="mt-1 text-lg font-medium">{{ $transportRequest->requester->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Pickup</dt>
                            <dd class="mt-1 text-lg">{{ $transportRequest->pickup_location }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Drop-off</dt>
                            <dd class="mt-1 text-lg">{{ $transportRequest->dropoff_location }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Pickup time</dt>
                            <dd class="mt-1 text-lg">{{ $transportRequest->pickup_time->format('d M Y · H:i') }}</dd>
                        </div>
                        @if ($transportRequest->purpose)
                            <div class="col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Purpose</dt>
                                <dd class="mt-1 text-lg whitespace-pre-line">{{ $transportRequest->purpose }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <!-- Passengers -->
                @if ($transportRequest->passengers->isNotEmpty())
                    <div class="bg-white rounded-2xl shadow p-8">
                        <h2 class="text-2xl font-bold mb-6">Passengers</h2>
                        <div class="space-y-4">
                            @foreach ($transportRequest->passengers as $p)
                                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                                    <div>
                                        <p class="font-medium">{{ $p->passenger_name }}</p>
                                        @if ($p->user)
                                            <p class="text-sm text-gray-600">{{ $p->user->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Assigned Trips -->
                @if ($transportRequest->trips->isNotEmpty())
                    <div class="bg-white rounded-2xl shadow p-8">
                        <h2 class="text-2xl font-bold mb-6">Assigned Trip(s)</h2>
                        @foreach ($transportRequest->trips as $trip)
                            <div class="p-6 bg-gray-50 rounded-xl mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <p class="font-medium">Vehicle</p>
                                        <p class="text-lg">{{ $trip->vehicle->plate_number }} ({{ $trip->vehicle->make ?? '' }} {{ $trip->vehicle->model ?? '' }})</p>
                                    </div>
                                    <div>
                                        <p class="font-medium">Driver</p>
                                        <p class="text-lg">{{ $trip->driver->name }}</p>
                                    </div>
                                    <div>
                                        <p class="font-medium">Departure</p>
                                        <p class="text-lg">{{ $trip->departure_time->format('d M Y H:i') }}</p>
                                    </div>
                                    <div>
                                        <p class="font-medium">Status</p>
                                        <span class="inline-flex px-4 py-1 rounded-full text-sm
                                            {{ match ($trip->status) {
                                                'completed' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                                'active'    => 'bg-blue-100 text-blue-800',
                                                default     => 'bg-yellow-100 text-yellow-800'
                                            } }}">
                                            {{ ucfirst($trip->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Actions sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow p-8 sticky top-8">
                    <h2 class="text-2xl font-bold mb-6">Actions</h2>

                    @if ($transportRequest->isPending())
                        <!-- Approve -->
                        <form action="{{ route('admin.transport-requests.approve', $transportRequest) }}" method="POST" class="mb-6">
                            @csrf
                            <button type="submit"
                                    class="w-full px-6 py-4 bg-green-600 text-white font-medium rounded-xl hover:bg-green-700 transition">
                                Approve Request
                            </button>
                        </form>

                        <!-- Reject -->
                        <form action="{{ route('admin.transport-requests.reject', $transportRequest) }}" method="POST">
                            @csrf
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason</label>
                            <textarea name="admin_notes" rows="3" required
                                      class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
                            <button type="submit" class="mt-4 w-full px-6 py-4 bg-red-600 text-white font-medium rounded-xl hover:bg-red-700 transition">
                                Reject Request
                            </button>
                        </form>
                    @endif

                    @if ($transportRequest->isPending() || $transportRequest->isApproved())
                        <div class="mt-10">
                            <h3 class="text-xl font-bold mb-4">Assign Vehicle & Driver</h3>
                            <form action="{{ route('admin.transport-requests.assign', $transportRequest) }}" method="POST">
                                @csrf

                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Vehicle</label>
                                    <select name="vehicle_id" required
                                            class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                                        <option value="">Select vehicle</option>
                                        @foreach ($availableVehicles as $v)
                                            <option value="{{ $v->id }}">
                                                {{ $v->plate_number }} — {{ $v->make ?? '' }} {{ $v->model ?? '' }}
                                                @if (!$v->isAvailable()) (Unavailable) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Driver</label>
                                    <select name="driver_id" required
                                            class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                                        <option value="">Select driver</option>
                                        @foreach ($availableDrivers as $d)
                                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Departure Time</label>
                                    <input type="datetime-local" name="departure_time" required
                                           class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                                </div>

                                <button type="submit"
                                        class="w-full px-6 py-4 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition">
                                    Assign Trip
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection