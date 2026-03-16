@extends('layouts.app')

@section('title', 'Driver Dashboard')

@section('content')
<div class="min-h-screen bg-blue-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Welcome Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 mb-12">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div>
                    <h1 class="text-3xl font-semibold text-gray-900 tracking-tight">Driver Dashboard</h1>
                    <p class="mt-2 text-lg text-gray-700">
                        Welcome back, <span class="font-medium">{{ auth()->user()->name }}</span>
                    </p>
                </div>
                <div class="text-right text-sm text-gray-500">
                    Last login: {{ now()->format('d M Y • H:i') }}
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                <h3 class="text-lg font-medium text-gray-700">Pending Fuel Requests</h3>
                <p class="text-4xl font-bold text-amber-600 mt-2">0</p>
                <p class="text-sm text-gray-500 mt-1">Awaiting approval</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                <h3 class="text-lg font-medium text-gray-700">Approved Requests</h3>
                <p class="text-4xl font-bold text-green-600 mt-2">0</p>
                <p class="text-sm text-gray-500 mt-1">Ready to complete</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                <h3 class="text-lg font-medium text-gray-700">Payment Pending</h3>
                <p class="text-4xl font-bold text-purple-600 mt-2">0</p>
                <p class="text-sm text-gray-500 mt-1">Awaiting admin review</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                <h3 class="text-lg font-medium text-gray-700">Active Trips</h3>
                <p class="text-4xl font-bold text-indigo-600 mt-2">0</p>
                <p class="text-sm text-gray-500 mt-1">Ongoing assignments</p>
            </div>
        </div>

        <!-- Main Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Fuel Requests -->
            <a href="{{ route('driver.fuel-requests.index') }}"
               class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 hover:shadow-md hover:border-indigo-200 transition group">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 group-hover:text-indigo-600 transition">Fuel Requests</h3>
                        <p class="text-gray-600 mt-1">Request fuel and track approvals</p>
                    </div>
                </div>
                <p class="text-sm text-gray-500">View history, complete fill-ups & request payments</p>
            </a>

            <!-- Placeholder: My Trips -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 opacity-75">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">My Trips</h3>
                        <p class="text-gray-600 mt-1">View upcoming & completed trips</p>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-4">Coming soon – Trip management</p>
            </div>

            <!-- Placeholder: Vehicle Info -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 opacity-75">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Vehicle Status</h3>
                        <p class="text-gray-600 mt-1">Location & maintenance</p>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-4">Coming soon – Real-time tracking</p>
            </div>

            <!-- Placeholder: Notifications -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 opacity-75">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Notifications</h3>
                        <p class="text-gray-600 mt-1">Alerts & messages</p>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-4">Coming soon – Messages & alerts</p>
            </div>
        </div>
    </div>
</div>
@endsection