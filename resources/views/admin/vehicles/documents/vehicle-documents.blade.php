@extends('layouts.admin')

@section('title', 'Documents for ' . $vehicle->plate_number)

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Header with Vehicle Info -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 space-y-4 sm:space-y-0">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Vehicle Documents</h1>
                <p class="mt-2 text-lg text-gray-700">
                    Showing all documents for: 
                    <span class="font-semibold text-indigo-600">{{ $vehicle->plate_number }}</span>
                    ({{ $vehicle->make ?? 'N/A' }} {{ $vehicle->model ?? '' }})
                </p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('admin.vehicledocuments.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-xl shadow-md hover:bg-indigo-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Upload New
                </a>
                <a href="{{ route('admin.vehicledocuments.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gray-200 text-gray-800 font-medium rounded-xl hover:bg-gray-300 transition">
                    View All Documents
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-5 rounded-2xl mb-8 flex items-center">
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
                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Document #</th>
                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Issue Date</th>
                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Expiry Date</th>
                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-8 py-5 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($documents as $doc)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-8 py-6 font-medium">
                                {{ ucfirst(str_replace('_', ' ', $doc->document_type)) }}
                            </td>
                            <td class="px-8 py-6">
                                {{ $doc->document_number ?? '—' }}
                            </td>
                            <td class="px-8 py-6">
                                {{ $doc->issue_date ? $doc->issue_date->format('d M Y') : 'Not set' }}
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
                                <a href="{{ route('admin.vehicledocuments.show', $doc) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">View</a>
                                <a href="{{ route('admin.vehicledocuments.edit', $doc) }}" class="text-amber-600 hover:text-amber-900 mr-4">Edit</a>
                                <form action="{{ route('admin.vehicledocuments.destroy', $doc) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" 
                                            onclick="return confirm('Delete this document?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-16 text-center text-gray-500 text-lg">
                                No documents uploaded for this vehicle yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="px-8 py-6 border-t border-gray-200">
                {{ $documents->links() }}
            </div>
        </div>
    </div>
@endsection