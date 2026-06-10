@extends('layouts.admin')

@section('title', 'Edit Worker')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900">
                Edit Worker: {{ $worker->name ?? '—' }}
            </h1>
            <a href="{{ route('admin.workers.index') }}" 
               class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Workers
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-5 rounded-2xl mb-10">
                <p class="font-medium mb-2">Please correct the following:</p>
                <ul class="list-disc pl-6 space-y-1.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.workers.update', $worker) }}" 
              class="bg-white rounded-2xl shadow-lg p-10 space-y-10 border border-gray-100">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- User Information -->
                <div>
                    <label for="name" class="block text-lg font-medium text-gray-700 mb-3">Full Name *</label>
                    <input type="text" name="name" id="name" 
                           value="{{ old('name', $worker->name) }}" required
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    @error('name') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-lg font-medium text-gray-700 mb-3">Email *</label>
                    <input type="email" name="email" id="email" 
                           value="{{ old('email', $worker->email) }}" required
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    @error('email') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="block text-lg font-medium text-gray-700 mb-3">Phone</label>
                    <input type="tel" name="phone" id="phone" 
                           value="{{ old('phone', $worker->phone) }}"
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    @error('phone') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="block text-lg font-medium text-gray-700 mb-3">New Password (optional)</label>
                    <input type="password" name="password" id="password"
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    @error('password') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-lg font-medium text-gray-700 mb-3">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
            </div>

            <hr class="my-8 border-gray-200">

            <!-- Worker Profile Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="work_id" class="block text-lg font-medium text-gray-700 mb-3">Work ID</label>
                    <input type="text" name="work_id" id="work_id" 
                           value="{{ old('work_id', $worker->worker?->work_id) }}"
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    @error('work_id') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="nin" class="block text-lg font-medium text-gray-700 mb-3">NIN</label>
                    <input type="text" name="nin" id="nin" 
                           value="{{ old('nin', $worker->worker?->nin) }}"
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    @error('nin') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="department" class="block text-lg font-medium text-gray-700 mb-3">Department</label>
                    <input type="text" name="department" id="department" 
                           value="{{ old('department', $worker->worker?->department) }}"
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    @error('department') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="position" class="block text-lg font-medium text-gray-700 mb-3">Position</label>
                    <input type="text" name="position" id="position" 
                           value="{{ old('position', $worker->worker?->position) }}"
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    @error('position') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="hire_date" class="block text-lg font-medium text-gray-700 mb-3">Hire Date</label>
                    <input type="date" name="hire_date" id="hire_date" 
                           value="{{ old('hire_date', $worker->worker?->hire_date?->format('Y-m-d')) }}"
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    @error('hire_date') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="employment_type" class="block text-lg font-medium text-gray-700 mb-3">Employment Type</label>
                    <select name="employment_type" id="employment_type"
                            class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="permanent" {{ old('employment_type', $worker->worker?->employment_type) === 'permanent' ? 'selected' : '' }}>Permanent</option>
                        <option value="contract" {{ old('employment_type', $worker->worker?->employment_type) === 'contract' ? 'selected' : '' }}>Contract</option>
                        <option value="casual" {{ old('employment_type', $worker->worker?->employment_type) === 'casual' ? 'selected' : '' }}>Casual</option>
                        <option value="probation" {{ old('employment_type', $worker->worker?->employment_type) === 'probation' ? 'selected' : '' }}>Probation</option>
                    </select>
                </div>
            </div>

            <div class="pt-8 flex justify-end space-x-6">
                <a href="{{ route('admin.workers.index') }}" 
                   class="px-10 py-5 bg-gray-200 text-gray-800 rounded-xl hover:bg-gray-300 transition font-medium">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-12 py-5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium shadow-md">
                    Update Worker
                </button>
            </div>
        </form>
    </div>
@endsection