@extends('layouts.admin')

@section('title', 'Users')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Users</h1>
            <p class="mt-2 text-gray-600">Manage all system users and their roles</p>
        </div>

        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-xl shadow-md hover:bg-indigo-700 hover:shadow-lg transition-all duration-200">

            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>

            Add New User
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-8 bg-green-50 border border-green-200 text-green-800 px-6 py-5 rounded-2xl flex items-center">
            <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif


    <!-- Users Table Card -->
    <div class="bg-white rounded-2xl shadow border border-gray-100">

        <!-- Horizontal Scroll Wrapper -->
        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">
                    <tr>

                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            User
                        </th>

                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Email
                        </th>

                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Phone
                        </th>

                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Role
                        </th>

                        <th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status
                        </th>

                        <th class="px-8 py-5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Actions
                        </th>

                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">

                    @forelse ($users as $user)

                        <tr class="hover:bg-gray-50 transition-colors duration-150">

                            <!-- User -->
                            <td class="px-8 py-6 whitespace-nowrap">

                                <div class="flex items-center">

                                    <div class="flex-shrink-0 h-12 w-12">

                                        
                                    @if ($user->profile_photo_path)

                                    <img
                                        src="{{ asset('storage/' . $user->profile_photo_path) }}"
                                        class="h-12 w-12 rounded-full object-cover">

                                    @else

                                    <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold">
                                        {{ strtoupper(substr($user->name,0,1)) }}
                                    </div>

                                    @endif

                                    </div>

                                    <div class="ml-5">

                                        <div class="text-lg font-medium text-gray-900">
                                            {{ $user->name }}
                                        </div>

                                        <div class="text-sm text-gray-600">
                                            {{ $user->email }}
                                        </div>

                                    </div>

                                </div>

                            </td>


                            <!-- Email -->
                            <td class="px-8 py-6 whitespace-nowrap text-gray-600">
                                {{ $user->email }}
                            </td>


                            <!-- Phone -->
                            <td class="px-8 py-6 whitespace-nowrap text-gray-600">
                                {{ $user->phone ?? '—' }}
                            </td>


                            <!-- Role -->
                            <td class="px-8 py-6 whitespace-nowrap">

                                <span class="px-4 py-1.5 inline-flex text-sm font-medium rounded-full

                                {{ $user->role === 'admin_level_1' ? 'bg-blue-100 text-blue-800 border border-blue-200' :
                                   ($user->role === 'admin_level_2' ? 'bg-purple-100 text-purple-800 border border-purple-200' :
                                   ($user->role === 'driver' ? 'bg-green-100 text-green-800 border border-green-200' :
                                   ($user->role === 'worker' ? 'bg-orange-100 text-orange-800 border border-orange-200' :
                                   'bg-gray-100 text-gray-800 border border-gray-200'))) }}">

                                   {{ $user->role_display }}

                                </span>

                            </td>


                            <!-- Status -->
                            <td class="px-8 py-6 whitespace-nowrap">

                                <span class="px-4 py-1.5 inline-flex text-sm font-medium rounded-full

                                {{ $user->is_active
                                    ? 'bg-green-100 text-green-800 border border-green-200'
                                    : 'bg-red-100 text-red-800 border border-red-200' }}">

                                    {{ $user->is_active ? 'Active' : 'Inactive' }}

                                </span>

                            </td>


                            <!-- Actions -->
                            <td class="px-8 py-6 whitespace-nowrap text-right text-sm font-medium">

                               

                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="text-amber-600 hover:text-amber-900 mr-4">
                                    Edit
                                </a>

                                <form action="{{ route('admin.users.destroy', $user) }}"
                                      method="POST"
                                      class="inline">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Delete user {{ $user->name }}? This cannot be undone.')">

                                        Delete

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="px-8 py-16 text-center text-gray-500 text-lg">
                                No users found yet. Click "Add New User" to begin.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <!-- Pagination -->
        <div class="px-8 py-6 border-t border-gray-200">
            {{ $users->appends(request()->query())->links('pagination::tailwind') }}
        </div>

    </div>

</div>

@endsection