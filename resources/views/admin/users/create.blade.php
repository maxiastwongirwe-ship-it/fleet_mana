@extends('layouts.admin')

@section('title', 'Create User')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900">Create New User</h1>
            <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-lg">
                ← Back to Users
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-5 rounded-2xl mb-10">
                <ul class="list-disc pl-5 space-y-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-lg p-10 space-y-10">
            @csrf

            <!-- Profile Photo -->
            <div class="flex flex-col sm:flex-row items-center gap-8">
                <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-100 flex-shrink-0 border-2 border-gray-200">
                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-4xl font-medium">
                        ?
                    </div>
                </div>
                <div class="flex-1">
                    <label for="photo" class="block text-xl font-medium text-gray-700 mb-3">Profile Photo</label>
                    <input type="file" name="photo" id="photo" accept="image/*" 
                           class="block w-full text-base text-gray-900 file:mr-6 file:py-4 file:px-8 file:rounded-xl file:border-0 file:text-base file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition file:cursor-pointer cursor-pointer">
                    <p class="mt-3 text-sm text-gray-500">JPG, PNG, max 2MB. Optional.</p>
                    @error('photo')
                        <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="name" class="block text-xl font-medium text-gray-700 mb-3">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    @error('name')
                        <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-xl font-medium text-gray-700 mb-3">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    @error('email')
                        <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Phone & Role -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="phone" class="block text-xl font-medium text-gray-700 mb-3">Phone Number</label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    @error('phone')
                        <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="role" class="block text-xl font-medium text-gray-700 mb-3">Role</label>
                    <select name="role" id="role" required 
                            class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="driver">Driver</option>
                        <option value="worker">Worker / Passenger</option>
                        <option value="admin_level_1">Admin Level 1</option>
                        <option value="admin_level_2">Admin Level 2 (Super Admin)</option>
                    </select>
                    @error('role')
                        <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Password -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="password" class="block text-xl font-medium text-gray-700 mb-3">Password</label>
                    <input type="password" name="password" id="password" required 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    @error('password')
                        <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xl font-medium text-gray-700 mb-3">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    @error('password_confirmation')
                        <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Active Status -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked 
                       class="h-6 w-6 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <label for="is_active" class="ml-4 text-xl text-gray-700 font-medium">Active (can log in)</label>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-10 flex justify-end space-x-6">
                <a href="{{ route('admin.users.index') }}" 
                   class="px-10 py-5 bg-gray-200 text-gray-800 font-medium rounded-xl hover:bg-gray-300 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-12 py-5 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition shadow-md">
                    Create User
                </button>
            </div>
        </form>
    </div>
@endsection