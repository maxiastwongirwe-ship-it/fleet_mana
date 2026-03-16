@extends('layouts.admin')

@section('title', 'Drivers')

@section('content')
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 space-y-4 sm:space-y-0">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Drivers</h1>
                <p class="mt-2 text-gray-600">Manage driver profiles, licenses, and compliance</p>
            </div>
            <a href="{{ route('admin.drivers.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-xl shadow-md hover:bg-indigo-700 hover:shadow-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Driver Profile
            </a>
        </div>

        @if (session('success'))
            <div class="mb-8 bg-green-50 border border-green-200 text-green-800 px-6 py-5 rounded-2xl flex items-center">
                <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow overflow-hidden border border-gray-100">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Driver</th>
                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">License</th>
                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Expiry</th>
                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-8 py-5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($drivers as $driver)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        @if ($driver->driver_photo_path)
                                            <img class="h-12 w-12 rounded-full object-cover ring-2 ring-white" 
                                                 src="{{ Storage::url($driver->driver_photo_path) }}" alt="">
                                        @else
                                            <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-medium text-xl ring-2 ring-white">
                                                {{ substr($driver->user->name ?? 'D', 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-5">
                                        <div class="text-lg font-medium text-gray-900">{{ $driver->user->name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-600">{{ $driver->user->email ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-gray-700">
                                {{ $driver->license_number ?? '—' }}
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-gray-600">
                                @if ($driver->license_expiry_date)
                                    @php
                                        $expiry = \Carbon\Carbon::parse($driver->license_expiry_date);
                                    @endphp
                                    {{ $expiry->format('d M Y') }}
                                @else
                                    '—'
                                @endif
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="px-4 py-1.5 inline-flex text-sm font-medium rounded-full 
                                    {{ $driver->status === 'active' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                    {{ ucfirst($driver->status ?? 'unknown') }}
                                </span>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.drivers.show', $driver) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">View</a>
                                <a href="{{ route('admin.drivers.edit', $driver) }}" class="text-amber-600 hover:text-amber-900 mr-4">Edit</a>
                                <form action="{{ route('admin.drivers.destroy', $driver) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Delete driver profile for {{ $driver->user->name ?? 'this driver' }}?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-16 text-center text-gray-500 text-lg">
                                No driver profiles yet. Click "Add Driver Profile" to begin.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="px-8 py-6 border-t border-gray-200">
                {{ $drivers->appends(request()->query())->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
@endsection