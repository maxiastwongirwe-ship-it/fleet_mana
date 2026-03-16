@extends('layouts.admin')

@section('title', 'Create Driver Profile')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900">Create Driver Profile</h1>
            <a href="{{ route('admin.drivers.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Drivers
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-5 rounded-2xl mb-10">
                <p class="font-medium mb-2">Please fix the following errors:</p>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.drivers.store') }}" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-lg p-10 space-y-10">
            @csrf

            <!-- User Selection -->
            <div>
                <label for="user_id" class="block text-lg font-medium text-gray-700 mb-3">Select User (Driver Role Only)</label>
                <select name="user_id" id="user_id" required 
                        class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <option value="">Choose a driver user...</option>
                    @foreach ($availableUsers as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                @error('user_id') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <!-- Photo Upload -->
            <div>
                <label for="driver_photo" class="block text-lg font-medium text-gray-700 mb-3">Driver Photo</label>
                <input type="file" name="driver_photo" id="driver_photo" accept="image/*" 
                       class="block w-full text-base text-gray-900 file:mr-4 file:py-4 file:px-8 file:rounded-xl file:border-0 file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer cursor-pointer">
                <p class="mt-3 text-sm text-gray-500">Recommended: clear face photo. Max 2MB.</p>
            </div>

            <!-- License Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="license_number" class="block text-lg font-medium text-gray-700 mb-3">License Number</label>
                    <input type="text" name="license_number" id="license_number" required 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    @error('license_number') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="license_category" class="block text-lg font-medium text-gray-700 mb-3">License Category</label>
                    <input type="text" name="license_category" id="license_category" 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="license_issue_date" class="block text-lg font-medium text-gray-700 mb-3">Issue Date</label>
                    <input type="date" name="license_issue_date" id="license_issue_date" 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>

                <div>
                    <label for="license_expiry_date" class="block text-lg font-medium text-gray-700 mb-3">Expiry Date</label>
                    <input type="date" name="license_expiry_date" id="license_expiry_date" 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    @error('license_expiry_date') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- NIN & Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="nin_number" class="block text-lg font-medium text-gray-700 mb-3">NIN Number</label>
                    <input type="text" name="nin_number" id="nin_number" 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>

                <div>
                    <label for="status" class="block text-lg font-medium text-gray-700 mb-3">Status</label>
                    <select name="status" id="status" required 
                            class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                        <option value="expired">Expired</option>
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
                    Save Driver Profile
                </button>
            </div>
        </form>
    </div>
@endsection