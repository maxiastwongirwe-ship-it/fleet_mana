@extends('layouts.admin')

@section('title', 'Edit Document')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900">Edit Document</h1>
            <a href="{{ route('admin.vehicledocuments.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to All Documents
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

        <form method="POST" action="{{ route('admin.vehicledocuments.update', $vehicledocument) }}" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-lg p-10 space-y-10">
            @csrf
            @method('PUT')

            <!-- Vehicle Selection -->
            <div>
                <label for="vehicle_id" class="block text-lg font-medium text-gray-700 mb-3">Attached Vehicle</label>
                <select name="vehicle_id" id="vehicle_id" required 
                        class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach ($vehicles as $v)
                        <option value="{{ $v->id }}" {{ old('vehicle_id', $vehicledocument->vehicle_id) == $v->id ? 'selected' : '' }}>
                            {{ $v->plate_number }} — {{ $v->make ?? 'N/A' }} {{ $v->model ?? '' }}
                        </option>
                    @endforeach
                </select>
                @error('vehicle_id') <p class="mt-2 text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <!-- Document Type -->
            <div>
                <label for="document_type" class="block text-lg font-medium text-gray-700 mb-3">Document Type</label>
                <select name="document_type" id="document_type" required 
                        class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="insurance" {{ old('document_type', $vehicledocument->document_type) == 'insurance' ? 'selected' : '' }}>Insurance</option>
                    <option value="third_party" {{ old('document_type', $vehicledocument->document_type) == 'third_party' ? 'selected' : '' }}>Third Party Insurance</option>
                    <option value="inspection" {{ old('document_type', $vehicledocument->document_type) == 'inspection' ? 'selected' : '' }}>Vehicle Inspection</option>
                    <option value="permit" {{ old('document_type', $vehicledocument->document_type) == 'permit' ? 'selected' : '' }}>Permit</option>
                    <option value="roadworthy" {{ old('document_type', $vehicledocument->document_type) == 'roadworthy' ? 'selected' : '' }}>Roadworthy Certificate</option>
                    <option value="license" {{ old('document_type', $vehicledocument->document_type) == 'license' ? 'selected' : '' }}>Vehicle License</option>
                    <option value="other" {{ old('document_type', $vehicledocument->document_type) == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <!-- Document Number -->
            <div>
                <label for="document_number" class="block text-lg font-medium text-gray-700 mb-3">Document Number / Policy #</label>
                <input type="text" name="document_number" id="document_number" value="{{ old('document_number', $vehicledocument->document_number) }}" 
                       class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Issue & Expiry Dates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="issue_date" class="block text-lg font-medium text-gray-700 mb-3">Issue Date</label>
                    <input type="date" name="issue_date" id="issue_date" value="{{ old('issue_date', $vehicledocument->issue_date?->format('Y-m-d')) }}" 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="expiry_date" class="block text-lg font-medium text-gray-700 mb-3">Expiry Date</label>
                    <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date', $vehicledocument->expiry_date?->format('Y-m-d')) }}" 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Current File & Re-upload -->
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-3">Current File</label>
                @if ($vehicledocument->file_path)
                    <a href="{{ Storage::url($vehicledocument->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium block mb-4">
                        View / Download Current File
                    </a>
                @else
                    <p class="text-gray-600">No file attached yet.</p>
                @endif

                <label for="file" class="block text-lg font-medium text-gray-700 mb-3">Replace File (optional)</label>
                <input type="file" name="file" accept=".pdf,image/*" 
                       class="block w-full text-base text-gray-900 file:mr-4 file:py-4 file:px-8 file:rounded-xl file:border-0 file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer cursor-pointer">
            </div>

            <!-- Valid & Notes -->
            <div class="flex items-center">
                <input type="checkbox" name="is_valid" value="1" {{ $vehicledocument->is_valid ? 'checked' : '' }} 
                       class="h-6 w-6 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <label for="is_valid" class="ml-4 text-lg font-medium text-gray-700">Document is currently valid</label>
            </div>

            <div>
                <label for="notes" class="block text-lg font-medium text-gray-700 mb-3">Notes</label>
                <textarea name="notes" rows="4" 
                          class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes', $vehicledocument->notes) }}</textarea>
            </div>

            <!-- Submit -->
            <div class="pt-10 flex justify-end space-x-6">
                <a href="{{ route('admin.vehicledocuments.index') }}" 
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