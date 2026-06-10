@extends('layouts.admin')

@section('title', 'Fuel Activity')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-semibold text-gray-900">Fuel Activity</h1>
                <p class="text-gray-500 mt-1">All fuel requests &amp; fill-ups with consumption monitoring</p>
            </div>
            
            <a href="{{ route('admin.fuel-logs.create') }}" 
               class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-2xl transition flex items-center gap-2">
                <span>Log New Fill-up</span>
                <span class="text-xl">+</span>
            </a>
        </div>

        <!-- Vehicle Filter -->
        <div class="bg-white rounded-3xl shadow-sm p-8 mb-8">
            <form method="GET" action="{{ route('admin.fuel-logs.index') }}">
                <div class="flex flex-wrap gap-6 items-end">
                    <div class="flex-1 min-w-[300px]">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Filter by Vehicle</label>
                        <select name="vehicle_id" onchange="this.form.submit()" 
                                class="w-full px-6 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-base">
                            <option value="">All Vehicles</option>
                            @foreach ($vehicles as $v)
                                <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>
                                    {{ $v->plate_number }} — {{ $v->make ?? '' }} {{ $v->model ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-8 py-4 bg-indigo-600 text-white rounded-2xl hover:bg-indigo-700 transition">
                        Apply Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                            <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Vehicle</th>
                            <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Driver</th>
                            <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Litres Dispensed</th>
                            <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Consumption</th>
                            <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Total Cost</th>
                            <th class="px-8 py-5 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($logs as $log)
                            @php
                                $fuelLog = $log->fuelLog; 
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ $fuelLog && $fuelLog->isSuspicious() ? 'bg-red-50' : '' }}">
                                <td class="px-8 py-6 text-sm">
                                    {{ $log->requested_at?->format('d M Y • H:i') ?? '—' }}
                                </td>
                                <td class="px-8 py-6 font-medium">
                                    {{ $log->vehicle->plate_number ?? 'N/A' }}
                                </td>
                                <td class="px-8 py-6 text-sm">
                                    {{ $fuelLog?->driver?->name ?? '—' }}
                                </td>
                                <td class="px-8 py-6 font-medium">
                                    @if($fuelLog)
                                        <span class="text-emerald-600">{{ number_format($fuelLog->litres_dispensed, 2) }} L</span>
                                    @elseif($log->actual_litres_dispensed)
                                        {{ number_format($log->actual_litres_dispensed, 2) }} L
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-8 py-6">
                                    @if($fuelLog && $fuelLog->litres_per_km)
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium">{{ $fuelLog->litres_per_km }} L/km</span>
                                            @if($fuelLog->isSuspicious())
                                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-2xl bg-red-100 text-red-700">⚠️ Suspicious</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6">
                                    <span class="inline-flex px-4 py-1.5 text-xs font-medium rounded-2xl
                                        @if($log->status === 'completed' || $log->status === 'payment_approved') bg-blue-100 text-blue-700
                                        @elseif($log->status === 'approved') bg-emerald-100 text-emerald-700
                                        @elseif($log->status === 'rejected' || $log->status === 'payment_rejected') bg-red-100 text-red-700
                                        @else bg-gray-100 text-gray-700 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $log->status)) }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    @if($fuelLog && $fuelLog->total_cost)
                                        <span class="font-medium">${{ number_format($fuelLog->total_cost, 2) }}</span>
                                    @elseif($log->total_cost)
                                        <span class="font-medium">${{ number_format($log->total_cost, 2) }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-right">
                                    @if($fuelLog)
                                        <a href="{{ route('admin.fuel-logs.show', $fuelLog) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 font-medium">
                                            View Log →
                                        </a>
                                    @else
                                        <a href="{{ route('admin.fuel-requests.show', $log) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 font-medium">
                                            View Request →
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-8 py-24 text-center">
                                    <div class="text-6xl mb-4">⛽</div>
                                    <p class="text-xl text-gray-400">No fuel activity found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-8 py-6 border-t border-gray-100">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
@endsection