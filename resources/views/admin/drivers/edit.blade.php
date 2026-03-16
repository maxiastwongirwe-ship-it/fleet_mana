@extends('layouts.admin')

@section('title', 'Edit Driver Profile')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900">Edit Driver Profile</h1>
            <a href="{{ route('admin.drivers.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Drivers
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-5 rounded-2xl mb-10">
                <p class="font-medium mb-2">Please correct the following:</p>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.drivers.update', $driver) }}" enctype="multipart/form-data" class="bg-white rounded-2xl shadow p-10 space-y-10">
            @csrf
            @method('PUT')

            <!-- Current Photo -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-10">
                <div class="flex-shrink-0">
                    <label class="block text-lg font-medium text-gray-700 mb-3">Current Driver Photo</label>
                    <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-100 border-2 border-gray-200">
                        @if ($driver->driver_photo_path)
                            <img src="{{ Storage::url($driver->driver_photo_path) }}" alt="" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-5xl font-medium">
                                {{ substr($driver->user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Add this field somewhere in the form -->
<div class="mb-6">
    <label class="flex items-center">
        <input type="checkbox" name="approved" value="1" 
               {{ old('approved', $driver->approved) ? 'checked' : '' }}
               class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <span class="ml-3 text-lg font-medium text-gray-700">
            Approve this driver for GPS tracking
        </span>
    </label>
    <p class="mt-1 text-sm text-gray-500">
        Only approved drivers can share real-time location.
    </p>
</div>

                <div class="flex-grow">
                    <label for="driver_photo" class="block text-lg font-medium text-gray-700 mb-3">Upload New Photo</label>
                    <input type="file" name="driver_photo" id="driver_photo" accept="image/*" 
                           class="block w-full text-base text-gray-900 file:mr-4 file:py-4 file:px-8 file:rounded-xl file:border-0 file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer cursor-pointer">
                    <p class="mt-3 text-sm text-gray-500">Optional. Will replace current photo.</p>
                </div>
            </div>

            <!-- License Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="license_number" class="block text-lg font-medium text-gray-700 mb-3">License Number</label>
                    <input type="text" name="license_number" id="license_number" value="{{ old('license_number', $driver->license_number) }}" required 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    @error('license_number') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="license_category" class="block text-lg font-medium text-gray-700 mb-3">License Category</label>
                    <input type="text" name="license_category" id="license_category" value="{{ old('license_category', $driver->license_category) }}" 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="license_issue_date" class="block text-lg font-medium text-gray-700 mb-3">Issue Date</label>
                    <input type="date" name="license_issue_date" id="license_issue_date" value="{{ old('license_issue_date', $driver->license_issue_date?->format('Y-m-d')) }}" 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>

                <div>
                    <label for="license_expiry_date" class="block text-lg font-medium text-gray-700 mb-3">Expiry Date</label>
                    <input type="date" name="license_expiry_date" id="license_expiry_date" value="{{ old('license_expiry_date', $driver->license_expiry_date?->format('Y-m-d')) }}" 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    @error('license_expiry_date') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- NIN & Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="nin_number" class="block text-lg font-medium text-gray-700 mb-3">NIN Number</label>
                    <input type="text" name="nin_number" id="nin_number" value="{{ old('nin_number') }}" 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>

                <div>
                    <label for="status" class="block text-lg font-medium text-gray-700 mb-3">Status</label>
                    <select name="status" id="status" required 
                            class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="active" {{ $driver->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="suspended" {{ $driver->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="expired" {{ $driver->status === 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-10 flex justify-end space-x-6">
                <a href="{{ route('admin.drivers.index') }}" 
                   class="px-10 py-5 bg-gray-200 text-gray-800 rounded-xl hover:bg-gray-300 transition font-medium">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-12 py-5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium shadow-md">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
@endsection