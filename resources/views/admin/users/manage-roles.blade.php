@extends('layouts.admin')

@section('title', 'Manage User Roles')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold mb-8">Manage User Roles & Approvals</h1>

        <div class="bg-white rounded-2xl shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Role</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->role }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="{{ $user->approved ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $user->approved ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <form action="{{ route('admin.users.update-role', $user) }}" method="POST">
                                    @csrf
                                    <select name="role" class="border rounded px-2 py-1">
                                        <option value="pending" {{ $user->role === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="driver" {{ $user->role === 'driver' ? 'selected' : '' }}>Driver</option>
                                        <option value="worker" {{ $user->role === 'worker' ? 'selected' : '' }}>Worker</option>
                                        @if (auth()->user()->role === 'admin_level_1')
                                            <option value="admin_level_1" {{ $user->role === 'admin_level_1' ? 'selected' : '' }}>Super Admin</option>
                                            <option value="admin_level_2" {{ $user->role === 'admin_level_2' ? 'selected' : '' }}>Admin Level 2</option>
                                        @endif
                                    </select>
                                    <label class="ml-4">
                                        <input type="checkbox" name="approved" value="1" {{ $user->approved ? 'checked' : '' }}>
                                        Approved
                                    </label>
                                    <button type="submit" class="ml-4 text-indigo-600 hover:text-indigo-900">
                                        Update
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No users to manage yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection