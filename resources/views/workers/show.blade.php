@extends('layouts.admin')

@section('title', 'Worker Details')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900">Worker: {{ $worker->user->name ?? '—' }}</h1>
            <a href="{{ route('workers.index') }}" 
               class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Workers
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-10 border border-gray-100 space-y-10">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Personal Information</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $worker->user->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $worker->user->email ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $worker->user->phone ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                @if ($worker->isActive())
                                    <span class="inline-flex px-4 py-1.5 rounded-full text-base font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex px-4 py-1.5 rounded-full text-base font-medium bg-red-100 text-red-800">
                                        Inactive
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Employment Details</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Work ID</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $worker->work_id ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">NIN</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $worker->nin ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Department</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $worker->department ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Position</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $worker->position ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Hire Date</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $worker->hire_date?->format('d M Y') ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Employment Type</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ ucfirst($worker->employment_type ?? '—') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-100 flex justify-end space-x-6">
                <a href="{{ route('workers.index') }}" 
                   class="px-10 py-5 bg-gray-200 text-gray-800 rounded-xl hover:bg-gray-300 transition font-medium">
                    Back to List
                </a>
                <a href="{{ route('workers.edit', $worker) }}" 
                   class="px-12 py-5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium shadow-md">
                    Edit Worker
                </a>
            </div>
        </div>
    </div>
@endsection