@extends('layouts.admin')

@section('title', 'Vehicle Documents')

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 space-y-4 sm:space-y-0">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Vehicle Documents</h1>
                <p class="mt-2 text-gray-600">All documents across the fleet</p>
            </div>
            <a href="{{ route('admin.vehicledocuments.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-xl shadow-md hover:bg-indigo-700 hover:shadow-lg transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Upload New Document
            </a>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="mb-8 bg-green-50 border border-green-200 text-green-800 px-6 py-5 rounded-2xl flex items-center">
                <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Documents Table -->
        <div class="bg-white rounded-2xl shadow overflow-hidden border border-gray-100">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Plate Number</th>
                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Document #</th>
                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Expiry</th>
                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-8 py-5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($documents as $doc)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-8 py-6 whitespace-nowrap font-medium text-gray-900">
                                {{ $doc->vehicle ? $doc->vehicle->plate_number : 'Unattached' }}
                            </td>
                            <td class="px-8 py-6 font-medium">
                                {{ ucfirst(str_replace('_', ' ', $doc->document_type)) }}
                            </td>
                            <td class="px-8 py-6">
                                {{ $doc->document_number ?? '—' }}
                            </td>
                            <td class="px-8 py-6">
                                {{ $doc->expiry_date ? $doc->expiry_date->format('d M Y') : 'No expiry' }}
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-4 py-1.5 inline-flex text-sm font-medium rounded-full 
                                    {{ $doc->expiry_date && $doc->expiry_date->isPast() ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-green-100 text-green-800 border border-green-200' }}">
                                    {{ $doc->expiry_date && $doc->expiry_date->isPast() ? 'Expired' : 'Valid' }}
                                </span>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-right text-sm font-medium">
                                <!-- View File -->
                                @if ($doc->file_url)
                                    <a href="{{ $doc->file_url }}" target="_blank" class="text-blue-600 hover:text-blue-900 mr-4">View File</a>
                                @endif

                                <!-- Details -->
                                <a href="{{ route('admin.vehicledocuments.show', $doc) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Details</a>

                                <!-- Edit -->
                                <a href="{{ route('admin.vehicledocuments.edit', $doc) }}" class="text-amber-600 hover:text-amber-900 mr-4">Edit</a>

                                <!-- Delete -->
                                <form action="{{ route('admin.vehicledocuments.destroy', $doc) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" 
                                            onclick="return confirm('Delete this document?')">
                                        Delete
                                    </button>
                                </form>

                                <!-- NEW BUTTON: View All Documents for This Vehicle -->
                                @if ($doc->vehicle)
                                    <a href="{{ route('admin.vehicledocuments.vehicle', $doc->vehicle) }}" 
                                       title="View all documents for this vehicle"
                                       class="ml-4 inline-flex items-center px-4 py-2 bg-purple-100 text-purple-800 rounded-lg hover:bg-purple-200 transition text-sm font-medium">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Vehicle Docs
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-16 text-center text-gray-500 text-lg">
                                No documents uploaded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="px-8 py-6 border-t border-gray-200">
                {{ $documents->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection