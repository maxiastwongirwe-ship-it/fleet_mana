@extends('layouts.admin')

@section('title', 'Upload New Document')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900">Upload New Document</h1>
            <a href="{{ route('admin.vehicledocuments.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Documents
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

        <form method="POST" action="{{ route('admin.vehicledocuments.store') }}" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-lg p-10 space-y-10">
            @csrf

            <!-- Vehicle Dropdown (required) -->
            <div>
                <label for="vehicle_id" class="block text-lg font-medium text-gray-700 mb-3">
                    Select Vehicle (by Plate Number)
                </label>
                <select name="vehicle_id" id="vehicle_id" required 
                        class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <option value="">-- Choose Vehicle --</option>
                    @foreach ($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                            {{ $vehicle->plate_number }} — {{ $vehicle->make ?? 'N/A' }} {{ $vehicle->model ?? '' }}
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
                    <option value="insurance">Insurance</option>
                    <option value="third_party">Third Party Insurance</option>
                    <option value="inspection">Vehicle Inspection</option>
                    <option value="permit">Permit</option>
                    <option value="roadworthy">Roadworthy Certificate</option>
                    <option value="license">Vehicle License</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <!-- Document Number -->
            <div>
                <label for="document_number" class="block text-lg font-medium text-gray-700 mb-3">Document Number / Policy #</label>
                <input type="text" name="document_number" id="document_number" value="{{ old('document_number') }}" 
                       class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="issue_date" class="block text-lg font-medium text-gray-700 mb-3">Issue Date</label>
                    <input type="date" name="issue_date" id="issue_date" value="{{ old('issue_date') }}" 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="expiry_date" class="block text-lg font-medium text-gray-700 mb-3">Expiry Date</label>
                    <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date') }}" 
                           class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <!-- File Upload -->
            <div>
                <label for="file" class="block text-lg font-medium text-gray-700 mb-3">Upload File (PDF or Image)</label>
                <input type="file" name="file" id="file" accept=".pdf,image/*" required 
                       class="block w-full text-base text-gray-900 file:mr-4 file:py-4 file:px-8 file:rounded-xl file:border-0 file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer cursor-pointer">
                <p class="mt-3 text-sm text-gray-500">Max 5MB. PDF, JPG, PNG, GIF recommended.</p>
            </div>

            <!-- Valid Checkbox & Notes -->
            <div class="flex items-start">
                <input type="checkbox" name="is_valid" id="is_valid" value="1" checked 
                       class="mt-1 h-6 w-6 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <label for="is_valid" class="ml-4 text-lg font-medium text-gray-700">
                    Currently Valid
                </label>
            </div>

            <div>
                <label for="notes" class="block text-lg font-medium text-gray-700 mb-3">Notes / Comments</label>
                <textarea name="notes" id="notes" rows="4" 
                          class="block w-full px-6 py-5 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <!-- Submit -->
            <div class="pt-10 flex justify-end">
                <button type="submit" 
                        class="px-12 py-5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium shadow-md">
                    Upload & Attach Document
                </button>
            </div>
        </form>
    </div>
@endsection