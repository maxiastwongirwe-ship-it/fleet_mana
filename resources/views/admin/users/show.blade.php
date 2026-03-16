@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">User Details</h1>
            <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                ← Back to Users
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow p-10 space-y-10">
            <!-- Header / Photo -->
            <div class="flex items-center">
                <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-100 flex-shrink-0">
                    @if ($user->profile_photo_path)
                        <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400 text-5xl font-medium">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                </div>

                <div class="ml-8">
                    <h2 class="text-4xl font-bold text-gray-900">{{ $user->name }}</h2>
                    <p class="text-xl text-gray-600 mt-2">{{ $user->email }}</p>
                    <p class="text-lg text-gray-500 mt-1">Phone: {{ $user->phone ?? 'Not provided' }}</p>
                </div>
            </div>

            <!-- Status Badges -->
            <div class="flex space-x-4">
                <span class="px-5 py-2 inline-flex text-lg font-medium rounded-full 
                    {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </span>

                <span class="px-5 py-2 inline-flex text-lg font-medium rounded-full 
                    {{ $user->role === 'admin_level_2' ? 'bg-purple-100 text-purple-800' : 
                       $user->role === 'admin_level_1' ? 'bg-blue-100 text-blue-800' : 
                       $user->role === 'driver' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                </span>
            </div>

            <!-- Driver Profile (if exists) -->
            @if ($user->driverProfile)
                <div class="border-t border-gray-200 pt-8">
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">Driver Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-lg text-gray-700"><strong>License Number:</strong> {{ $user->driverProfile->license_number }}</p>
                        </div>
                        <div>
                            <p class="text-lg text-gray-700"><strong>Status:</strong> {{ ucfirst($user->driverProfile->status) }}</p>
                        </div>
                        <div>
                            <p class="text-lg text-gray-700"><strong>License Expiry:</strong> {{ $user->driverProfile->license_expiry_date ? $user->driverProfile->license_expiry_date->format('d M Y') : 'Not set' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="pt-8 border-t border-gray-200 flex justify-end space-x-4">
                <a href="{{ route('admin.users.edit', $user) }}" 
                   class="px-8 py-4 bg-yellow-500 text-white rounded-xl hover:bg-yellow-600 transition">
                    Edit User
                </a>
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-8 py-4 bg-red-600 text-white rounded-xl hover:bg-red-700 transition" 
                            onclick="return confirm('Delete this user? This cannot be undone.')">
                        Delete User
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection