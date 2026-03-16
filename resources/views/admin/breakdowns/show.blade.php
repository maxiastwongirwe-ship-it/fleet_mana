@extends('layouts.admin')

@section('title', 'Breakdown Details - ' . ($breakdown->vehicle->plate_number ?? 'Unknown'))

@section('content')

<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-semibold text-gray-900 tracking-tight">Breakdown Details</h1>
        <a href="{{ route('admin.breakdowns.index') }}"
           class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 space-y-10">
            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Vehicle</h3>
                    <p class="mt-1.5 text-lg font-medium text-gray-900">{{ $breakdown->vehicle->plate_number }}</p>
                    <p class="text-sm text-gray-600">{{ $breakdown->vehicle->make }} {{ $breakdown->vehicle->model }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Driver</h3>
                    <p class="mt-1.5 text-lg font-medium text-gray-900">{{ $breakdown->driver?->name ?? '—' }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Location</h3>
                    <p class="mt-1.5 text-lg font-medium text-gray-900">{{ $breakdown->location ?? 'Not specified' }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Occurred At</h3>
                    <p class="mt-1.5 text-lg font-medium text-gray-900">{{ $breakdown->occurred_at->format('d M Y • H:i') }}</p>
                </div>
            </div>

            <!-- Severity & Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Severity</h3>
                    <div class="mt-1.5">
                        <span class="inline-flex px-4 py-1.5 text-sm font-medium rounded-full {{ 
                            match($breakdown->severity) {
                                'critical' => 'bg-red-50 text-red-700 ring-1 ring-red-200',
                                'major'    => 'bg-orange-50 text-orange-700 ring-1 ring-orange-200',
                                'moderate' => 'bg-yellow-50 text-yellow-700 ring-1 ring-yellow-200',
                                'minor'    => 'bg-green-50 text-green-700 ring-1 ring-green-200',
                                default => 'bg-gray-50 text-gray-700 ring-1 ring-gray-200',
                            }
                        }}">
                            {{ ucfirst($breakdown->severity) }}
                        </span>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Status</h3>
                    <div class="mt-1.5">
                        <span class="inline-flex px-4 py-1.5 text-sm font-medium rounded-full {{ $breakdown->status_class }} ring-1 ring-opacity-50">
                            {{ ucfirst(str_replace('_', ' ', $breakdown->status)) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Approval Section – now shows real approver name -->
            <div class="bg-gray-50 rounded-xl p-6 border border-gray-100">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Approval</h3>

                @if($breakdown->approved)
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0">
                            <span class="inline-flex px-5 py-2 text-base font-medium rounded-full bg-green-100 text-green-800">
                                Approved
                            </span>
                        </div>
                        <div>
                            <p class="text-gray-800">
                                Approved by <strong>{{ $breakdown->approvedBy?->name ?? 'Unknown Admin' }}</strong>
                            </p>
                            <p class="text-sm text-gray-500 mt-0.5">
                                {{ $breakdown->updated_at->format('d M Y • H:i') }}
                            </p>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <span class="inline-flex px-5 py-2 text-base font-medium rounded-full bg-yellow-100 text-yellow-800">
                            Pending Approval
                        </span>

                        @auth
                            @if(auth()->user()->isAdmin())
                                <form action="{{ route('admin.breakdowns.approve', $breakdown) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="px-6 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition shadow-sm">
                                        Approve This Report
                                    </button>
                                </form>
                            @endif
                        @else
                            <p class="text-sm text-gray-500 italic">Login as admin to approve this report.</p>
                        @endauth
                    </div>
                @endif
            </div>

            <!-- Description -->
            <div>
                <h3 class="text-lg font-medium text-gray-800 mb-3">Description</h3>
                <div class="prose prose-gray max-w-none">
                    {{ $breakdown->description ?: 'No description provided.' }}
                </div>
            </div>

            <!-- Costs -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-medium text-gray-800">Estimated Cost</h3>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ $breakdown->estimated_cost ? 'UGX ' . number_format($breakdown->estimated_cost, 0) : '—' }}
                    </p>
                </div>

                @if($breakdown->actual_cost !== null)
                    <div>
                        <h3 class="text-lg font-medium text-gray-800">Actual Cost</h3>
                        <p class="mt-2 text-2xl font-semibold text-gray-900">
                            UGX {{ number_format($breakdown->actual_cost, 0) }}
                        </p>
                    </div>
                @endif
            </div>

            <!-- Admin Notes -->
            @if($breakdown->admin_notes)
                <div>
                    <h3 class="text-lg font-medium text-gray-800 mb-3">Admin Notes</h3>
                    <div class="bg-gray-50 p-5 rounded-xl prose prose-gray max-w-none">
                        {{ $breakdown->admin_notes }}
                    </div>
                </div>
            @endif

            <!-- Photos -->
            <div>
                <h3 class="text-lg font-medium text-gray-800 mb-4">Photos</h3>

                @if(!empty($breakdown->photo_paths) && count($breakdown->photo_paths) > 0)
                    <div class="overflow-x-auto pb-6">
                        <div class="flex gap-6">
                            @foreach($breakdown->photo_paths as $path)
                                <div class="flex-shrink-0 w-72">
                                    <img
                                        src="/storage/{{ $path }}"
                                        alt="Breakdown photo"
                                        class="w-full h-72 object-cover rounded-2xl shadow-sm hover:shadow-md transition-shadow"
                                        loading="lazy"
                                    >
                                    <p class="mt-2 text-sm text-gray-600 text-center truncate">
                                        {{ basename($path) }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="bg-gray-50 border border-dashed border-gray-200 rounded-xl p-10 text-center">
                        <p class="text-gray-500 italic text-lg">No photos uploaded for this incident.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection