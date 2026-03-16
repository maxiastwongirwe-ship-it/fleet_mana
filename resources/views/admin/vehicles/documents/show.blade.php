@extends('layouts.admin')

@section('title', 'Document Details')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900">Document Details</h1>
            <div class="flex space-x-6">
                <a href="{{ route('admin.vehicledocuments.edit', $vehicledocument) }}" 
                   class="px-6 py-3 bg-amber-500 text-white rounded-xl hover:bg-amber-600 transition font-medium">
                    Edit Document
                </a>
                <form action="{{ route('admin.vehicledocuments.destroy', $vehicledocument) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition font-medium"
                            onclick="return confirm('Delete this document permanently?')">
                        Delete Document
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow p-10 space-y-12">
            <!-- Attached Vehicle -->
            <div class="border-b border-gray-200 pb-8">
                <h3 class="text-2xl font-semibold text-gray-900 mb-4">Attached Vehicle</h3>
                <p class="text-xl font-medium text-gray-800">
                    Plate Number: <span class="text-indigo-600">{{ $vehicledocument->vehicle->plate_number ?? 'Unattached' }}</span>
                </p>
                <p class="text-lg text-gray-600 mt-2">
                    Make/Model: {{ $vehicledocument->vehicle->make ?? 'N/A' }} {{ $vehicledocument->vehicle->model ?? '' }}
                </p>
            </div>

            <!-- Document Info -->
            <div>
                <h3 class="text-2xl font-semibold text-gray-900 mb-6">Document Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <p class="text-lg"><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $vehicledocument->document_type)) }}</p>
                    </div>
                    <div>
                        <p class="text-lg"><strong>Number / Policy #:</strong> {{ $vehicledocument->document_number ?? 'Not set' }}</p>
                    </div>
                    <div>
                        <p class="text-lg"><strong>Issue Date:</strong> {{ $vehicledocument->issue_date ? $vehicledocument->issue_date->format('d M Y') : 'Not set' }}</p>
                    </div>
                    <div>
                        <p class="text-lg"><strong>Expiry Date:</strong> {{ $vehicledocument->expiry_date ? $vehicledocument->expiry_date->format('d M Y') : 'No expiry' }}</p>
                    </div>
                    <div>
                        <p class="text-lg"><strong>Status:</strong> 
                            <span class="{{ $vehicledocument->expiry_date && $vehicledocument->expiry_date->isPast() ? 'text-red-600' : 'text-green-600' }}">
                                {{ $vehicledocument->expiry_date && $vehicledocument->expiry_date->isPast() ? 'Expired' : 'Valid' }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-lg"><strong>Uploaded By:</strong> {{ $vehicledocument->uploadedBy->name ?? 'System' }}</p>
                    </div>
                </div>
            </div>

            <!-- File -->
            <div class="border-t border-gray-200 pt-10">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6">Attached File</h3>
                @if ($vehicledocument->file_path)
                    <a href="{{ Storage::url($vehicledocument->file_path) }}" target="_blank" 
                       class="inline-flex items-center px-8 py-4 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition font-medium">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        View / Download Document
                    </a>
                @else
                    <p class="text-gray-600 italic">No file attached to this document.</p>
                @endif
            </div>

            <!-- Notes -->
            @if ($vehicledocument->notes)
                <div class="border-t border-gray-200 pt-10">
                    <h3 class="text-2xl font-semibold text-gray-900 mb-6">Notes</h3>
                    <p class="text-lg text-gray-700 whitespace-pre-line">{{ $vehicledocument->notes }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection