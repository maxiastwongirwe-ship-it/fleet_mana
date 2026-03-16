@extends('layouts.admin')

@section('title', 'Workers')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
            <h1 class="text-3xl font-bold text-gray-900">Workers</h1>
            <a href="{{ route('workers.create') }}"
               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-sm transition font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New Worker
            </a>
        </div>

        @if ($workers->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <p class="text-xl text-gray-600 mb-4">No workers found yet.</p>
                <a href="{{ route('workers.create') }}" class="text-indigo-600 hover:underline font-medium">
                    Create your first worker →
                </a>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-5 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-5 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">Work ID</th>
                                <th class="px-6 py-5 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-5 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">Position</th>
                                <th class="px-6 py-5 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-5 text-right text-sm font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($workers as $worker)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="text-base font-medium text-gray-900">{{ $worker->user->name ?? '—' }}</div>
                                        <div class="text-sm text-gray-500">{{ $worker->user->email ?? '—' }}</div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-base text-gray-700">
                                        {{ $worker->work_id ?? '—' }}
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-base text-gray-700">
                                        {{ $worker->department ?? '—' }}
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-base text-gray-700">
                                        {{ $worker->position ?? '—' }}
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        @if ($worker->isActive())
                                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('workers.show', $worker) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">View</a>
                                        <a href="{{ route('workers.edit', $worker) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                                        <form action="{{ route('workers.destroy', $worker) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('Delete this worker? This cannot be undone.')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-5 border-t border-gray-100">
                    {{ $workers->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection