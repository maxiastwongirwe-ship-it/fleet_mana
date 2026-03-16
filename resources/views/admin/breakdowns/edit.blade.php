@extends('layouts.admin')

@section('title', 'Edit Breakdown')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8 min-h-screen overflow-y-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-semibold text-gray-900 tracking-tight">Edit Breakdown</h1>
        <p class="mt-2 text-gray-600">Update incident information.</p>
    </div>

    <form action="{{ route('admin.breakdowns.update', $breakdown) }}" method="POST" enctype="multipart/form-data"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-8">
        @csrf @method('PUT')

        <!-- Same fields as create - with values filled -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Vehicle *</label>
                <select name="vehicle_id" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @foreach($vehicles as $v)
                        <option value="{{ $v->id }}" {{ $breakdown->vehicle_id == $v->id ? 'selected' : '' }}>
                            {{ $v->plate_number }} — {{ $v->make }} {{ $v->model }}
                        </option>
                    @endforeach
                </select>
                @error('vehicle_id') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Driver</label>
                <select name="driver_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">—</option>
                    @foreach($drivers as $d)
                        <option value="{{ $d->id }}" {{ $breakdown->driver_id == $d->id ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
                @error('driver_id') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- ... other fields like location, occurred_at, severity, description, costs, admin_notes, status ... -->
        <!-- (copy-paste from your previous edit blade and apply same styling: rounded-lg, mb-1.5, etc.) -->

        <!-- Current photos -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Current Photos</label>
            @if(!empty($breakdown->photo_paths) && count($breakdown->photo_paths))
                <div class="overflow-x-auto pb-4">
                    <div class="flex gap-4">
                        @foreach($breakdown->photo_paths as $path)
                            <div class="flex-shrink-0 w-40">
                                <img src="/storage/{{ $path }}" alt="Photo" class="w-40 h-40 object-cover rounded-xl shadow-sm">
                                <p class="mt-1.5 text-xs text-gray-500 text-center truncate">{{ basename($path) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-500 italic">No photos uploaded yet.</p>
            @endif
        </div>

        <!-- New photos upload (same as create) -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Add more photos</label>
            <!-- paste the same drop-area + preview + script logic as in create -->
        </div>

        <div class="flex justify-end gap-4 pt-6">
            <a href="{{ route('admin.breakdowns.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 shadow-sm transition">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection