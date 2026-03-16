@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Admin Dashboard</h1>
        <p class="text-lg text-gray-700">Welcome back, {{ auth()->user()->name }}. Manage the fleet here.</p>
        <!-- Add stats, maps, etc. later -->
    </div>
@endsection