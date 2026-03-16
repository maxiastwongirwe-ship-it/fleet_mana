@extends('layouts.admin')

@section('title', 'Driver: ' . $driver->user->name)

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900">Driver Profile</h1>
            <div class="flex space-x-6">
                <a href="{{ route('admin.drivers.edit', $driver) }}" 
                   class="px-6 py-3 bg-amber-500 text-white rounded-xl hover:bg-amber-600 transition font-medium">
                    Edit Profile
                </a>
                <form action="{{ route('admin.drivers.destroy', $driver) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition font-medium"
                            onclick="return confirm('Delete driver profile for {{ $driver->user->name }}? This cannot be undone.')">
                        Delete Profile
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow p-10 space-y-12">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-10">
                <div class="flex-shrink-0">
                    <div class="w-40 h-40 rounded-full overflow-hidden bg-gray-100 border-4 border-white shadow-lg">
                        @if ($driver->driver_photo_path)
                            <img src="{{ Storage::url($driver->driver_photo_path) }}" alt="" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-6xl font-medium">
                                {{ substr($driver->user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    <h2 class="text-4xl font-bold text-gray-900">{{ $driver->user->name }}</h2>
                    <p class="text-2xl text-gray-600 mt-2">{{ $driver->user->email }}</p>
                    <p class="text-xl text-gray-500 mt-1">Phone: {{ $driver->user->phone ?? 'Not provided' }}</p>
                </div>
            </div>

            <!-- Status Badges -->
            <div class="flex flex-wrap gap-4">
                <span class="px-6 py-2 inline-flex text-lg font-medium rounded-full bg-green-100 text-green-800 border border-green-200">
                    Driver
                </span>
                <span class="px-6 py-2 inline-flex text-lg font-medium rounded-full 
                    {{ $driver->status === 'active' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                    {{ ucfirst($driver->status) }}
                </span>
                <span class="px-6 py-2 inline-flex text-lg font-medium rounded-full 
                    {{ $driver->license_expiry_date && $driver->license_expiry_date->isPast() ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-green-100 text-green-800 border border-green-200' }}">
                    {{ $driver->licenseStatus }}
                </span>
            </div>

            <!-- License Details -->
            <div class="border-t border-gray-200 pt-10">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6">License Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <p class="text-lg"><strong>License Number:</strong> {{ $driver->license_number }}</p>
                    </div>
                    <div>
                        <p class="text-lg"><strong>Category:</strong> {{ $driver->license_category ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-lg"><strong>Issue Date:</strong> {{ $driver->license_issue_date ? $driver->license_issue_date->format('d M Y') : 'Not set' }}</p>
                    </div>
                    <div>
                        <p class="text-lg"><strong>Expiry Date:</strong> {{ $driver->license_expiry_date ? $driver->license_expiry_date->format('d M Y') : 'Not set' }}</p>
                    </div>
                    <div>
                        <p class="text-lg"><strong>NIN Number:</strong> {{ $driver->nin_number ? '********' : 'Not set' }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="pt-10 border-t border-gray-200 flex flex-wrap gap-6">
                <a href="{{ route('admin.drivers.edit', $driver) }}" 
                   class="px-8 py-4 bg-amber-500 text-white rounded-xl hover:bg-amber-600 transition font-medium flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Profile
                </a>

                <form action="{{ route('admin.drivers.destroy', $driver) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-8 py-4 bg-red-600 text-white rounded-xl hover:bg-red-700 transition font-medium flex items-center"
                            onclick="return confirm('Delete driver profile for {{ $driver->user->name }}?')">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Profile
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection