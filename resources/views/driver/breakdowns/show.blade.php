@extends('layouts.driver')

@section('title', 'Breakdown Report #{{ $breakdown->id }}')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    <div class="flex justify-between items-center">
        <h1 class="text-4xl font-semibold tracking-tight">Breakdown Report #{{ $breakdown->id }}</h1>
        <a href="{{ route('driver.breakdowns.index') }}" class="text-sky-600 hover:text-sky-700 font-medium">← Back to Reports</a>
    </div>

    <div class="apple-card p-10">

        <!-- Status -->
        <div class="flex justify-between items-center mb-10">
            <span class="px-8 py-3 rounded-3xl text-xl font-semibold {{ $breakdown->getStatusClassAttribute() }}">
                {{ ucfirst($breakdown->status) }}
            </span>
            
            @if($breakdown->approved_by)
                <span class="px-6 py-2 bg-emerald-100 text-emerald-700 rounded-3xl text-sm font-medium">
                    ✅ Approved by Admin
                </span>
            @elseif($breakdown->status === 'rejected')
                <span class="px-6 py-2 bg-red-100 text-red-700 rounded-3xl text-sm font-medium">
                    ❌ Rejected
                </span>
            @else
                <span class="px-6 py-2 bg-amber-100 text-amber-700 rounded-3xl text-sm font-medium">
                    ⏳ Awaiting Approval
                </span>
            @endif
        </div>

        <!-- Vehicle & Cost -->
        <div class="grid md:grid-cols-2 gap-10 mb-10">
            <div>
                <p class="text-sm text-gray-500">Vehicle</p>
                <p class="font-medium text-2xl">{{ $breakdown->vehicle->plate_number ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Estimated Repair Cost</p>
                <p class="font-medium text-3xl text-emerald-600">
                    UGX {{ number_format($breakdown->estimated_cost ?? 0) }}
                </p>
            </div>
        </div>

        <!-- Description -->
        <div class="mb-10">
            <p class="text-sm text-gray-500">Description</p>
            <p class="mt-4 text-gray-700 leading-relaxed">{{ $breakdown->description }}</p>
        </div>

        <!-- Location & Date -->
        <div class="grid md:grid-cols-2 gap-10 mb-10">
            <div>
                <p class="text-sm text-gray-500">Location</p>
                <p class="font-medium">{{ $breakdown->location ?? 'Not specified' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Occurred On</p>
                <p class="font-medium">{{ $breakdown->occurred_at->format('d M Y • H:i') }}</p>
            </div>
        </div>

        <!-- Admin Notes -->
        @if($breakdown->admin_notes)
            <div class="mt-10 pt-8 border-t">
                <h3 class="font-semibold mb-4">Admin Notes</h3>
                <div class="bg-gray-50 p-6 rounded-3xl">
                    <p class="text-gray-700">{{ $breakdown->admin_notes }}</p>
                </div>
            </div>
        @endif

        <!-- MARK AS REPAIRED & REQUEST PAYMENT -->
        @if($breakdown->approved_by && in_array($breakdown->status, ['acknowledged', 'in_progress', 'reported']))
            <div class="mt-12 pt-10 border-t bg-emerald-50 rounded-3xl p-8">
                <h3 class="font-semibold text-emerald-700 mb-6">Has the vehicle been repaired?</h3>
                
               <form method="POST" action="{{ route('driver.breakdowns.mark-repaired', $breakdown) }}" enctype="multipart/form-data">
    @csrf

    <div class="space-y-8">
        <!-- Actual Cost -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Actual Repair Cost (UGX)
            </label>
            <input type="number" name="actual_cost" step="0.01" 
                   class="w-full px-5 py-4 border border-gray-300 rounded-2xl"
                   placeholder="Enter actual cost (leave blank to use estimated)">
        </div>

        <!-- Repair Photos -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Upload Repair Photos <span class="text-red-500">*</span> (Minimum 2)
            </label>
            <input type="file" name="repair_photos[]" multiple accept="image/*" required
                   class="w-full px-5 py-4 border border-gray-300 rounded-2xl">
            <p class="text-xs text-gray-500 mt-2">Upload at least two clear photos of the repaired vehicle</p>
        </div>
    </div>

    <button type="submit" 
            class="mt-10 w-full py-5 bg-emerald-600 hover:bg-emerald-700 text-black font-semibold rounded-3xl text-lg transition">
        ✅ Mark as Repaired & Request Payment
    </button>
</form>
            </div>
        @endif

        <!-- Original Photos -->
        @if($breakdown->photo_paths && count($breakdown->photo_paths) > 0)
            <div class="mt-12">
                <h3 class="font-semibold mb-4">Original Breakdown Photos</h3>
                <div class="grid grid-cols-2 gap-6">
                    @foreach($breakdown->photo_paths as $path)
                        <img src="{{ Storage::url($path) }}" 
                             alt="Breakdown photo" 
                             class="rounded-3xl shadow border border-gray-200">
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>
@endsection