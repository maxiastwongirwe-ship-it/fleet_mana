@extends('layouts.admin')

@section('title', 'Fuel Logs')

@section('content')
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900">Fuel Logs</h1>
            <a href="{{ route('admin.fuel-logs.create') }}" 
               class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium">
                Log New Fill-up
            </a>
        </div>

        <!-- Vehicle Filter -->
        <div class="mb-8 bg-white rounded-2xl shadow p-6">
            <form method="GET" action="{{ route('admin.fuel-logs.index') }}">
                <div class="flex flex-wrap gap-6">
                    <div class="flex-grow">
                        <label for="vehicle_id" class="block text-lg font-medium text-gray-700 mb-3">
                            Filter by Vehicle
                        </label>
                        <select name="vehicle_id" id="vehicle_id" 
                                class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- All Vehicles --</option>
                            @foreach (\App\Models\Vehicle::orderBy('plate_number')->get() as $v)
                                <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>
                                    {{ $v->plate_number }} — {{ $v->make ?? 'N/A' }} {{ $v->model ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="self-end">
                        <button type="submit" class="px-8 py-5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium">
                            Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Vehicle</th>
                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Driver</th>
                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Litres</th>
                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Odometer</th>
                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Filled At</th>
                        <th class="px-8 py-5 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-8 py-6 font-medium">
                                {{ $log->vehicle->plate_number }}
                            </td>
                            <td class="px-8 py-6">
                                {{ $log->driver ? $log->driver->name : 'N/A' }}
                            </td>
                            <td class="px-8 py-6">
                                {{ number_format($log->litres_dispensed, 2) }} L
                            </td>
                            <td class="px-8 py-6">
                                {{ $log->odometer_reading }} km
                                @if ($log->distanceSinceLast)
                                    <span class="text-sm text-gray-500 ml-2">
                                        (+{{ $log->distanceSinceLast }} km)
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-6">
                                {{ $log->filled_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-8 py-6 text-right text-sm font-medium">
                                <a href="{{ route('admin.fuel-logs.show', $log) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-16 text-center text-gray-500 text-lg">
                                No fuel fill-ups logged yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="px-8 py-6 border-t border-gray-200">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
@endsection