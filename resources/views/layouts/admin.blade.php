<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - Fleet Admin</title>

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>

body{
    font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;
    background-color:#f5f5f7;
    margin:0;
    height:100vh;
    overflow:hidden;
}

/* Main app container */
.app-container{
    display:flex;
    height:100vh;
}

/* Sidebar */
.sidebar{
    width:280px;
    background:#ffffff;
    border-right:1px solid #e5e5e5;
    overflow-y:auto;
    flex-shrink:0;
}

/* Main content area */
.main-content{
    flex:1;
    min-height:0;
    overflow-x:auto;
    overflow-y:auto;
    -webkit-overflow-scrolling:touch;
}

/* Apple-style scrollbars */

.main-content::-webkit-scrollbar{
    width:8px;
    height:8px;
}

.main-content::-webkit-scrollbar-track{
    background:#f1f1f1;
    border-radius:10px;
}

.main-content::-webkit-scrollbar-thumb{
    background:#c1c1c1;
    border-radius:10px;
}

.main-content::-webkit-scrollbar-thumb:hover{
    background:#a8a8a8;
}

/* Firefox scrollbar */
*{
    scrollbar-width:thin;
    scrollbar-color:#c1c1c1 #f1f1f1;
}

/* Sidebar link styling */

.sidebar-link{
    transition:all .2s ease;
    border-radius:12px;
}

.sidebar-link:hover{
    background:#f5f5f7;
}

.sidebar-link.active{
    background:rgba(99,102,241,0.1);
    color:#6366f1;
    font-weight:600;
}

</style>

@yield('head')

</head>
<body>

<div class="app-container">

<!-- Sidebar -->

<aside class="sidebar p-6 flex flex-col gap-8">

<div class="flex items-center gap-3">

<div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-xl">
F
</div>

<h1 class="text-2xl font-bold text-gray-900">
Fleet Admin
</h1>

</div>

<nav class="flex flex-col gap-1">

<a href="{{ route('admin.dashboard') }}"
class="sidebar-link px-4 py-3 text-gray-700 flex items-center gap-3 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
Dashboard
</a>

<a href="{{ route('admin.users.index') }}"
class="sidebar-link px-4 py-3 text-gray-700 flex items-center gap-3 {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
Users & Roles
</a>

<a href="{{ route('admin.drivers.index') }}"
class="sidebar-link px-4 py-3 text-gray-700 flex items-center gap-3 {{ request()->routeIs('admin.drivers.*') ? 'active' : '' }}">
Drivers
</a>

<a href="{{ route('admin.workers.index') }}"
class="sidebar-link px-4 py-3 text-gray-700 flex items-center gap-3 {{ request()->routeIs('admin.workers.*') ? 'active' : '' }}">
Workers
</a>

<a href="{{ route('admin.vehicles.index') }}"
class="sidebar-link px-4 py-3 text-gray-700 flex items-center gap-3 {{ request()->routeIs('admin.vehicles.*') ? 'active' : '' }}">
Vehicles
</a>

<a href="{{ route('admin.vehicledocuments.index') }}"
class="sidebar-link px-4 py-3 text-gray-700 flex items-center gap-3 {{ request()->routeIs('admin.vehicledocuments.*') ? 'active' : '' }}">
Vehicle Documents
</a>

<a href="{{ route('admin.breakdowns.index') }}"
class="sidebar-link px-4 py-3 text-gray-700 flex items-center gap-3 {{ request()->routeIs('admin.breakdowns.*') ? 'active' : '' }}">
Breakdowns
</a>

<a href="{{ route('admin.fuel-requests.index') }}"
class="sidebar-link px-4 py-3 text-gray-700 flex items-center gap-3 {{ request()->routeIs('admin.fuel-requests.*') ? 'active' : '' }}">
Fuel Requests
</a>

<a href="{{ route('admin.fuel-logs.index') }}"
class="sidebar-link px-4 py-3 text-gray-700 flex items-center gap-3 {{ request()->routeIs('admin.fuel-logs.*') ? 'active' : '' }}">
Fuel Logs
</a>

<a href="{{ route('admin.transport-requests.index') }}"
class="sidebar-link px-4 py-3 text-gray-700 flex items-center gap-3 {{ request()->routeIs('admin.transport-requests.*') ? 'active' : '' }}">
Transport Requests
</a>

<a href="{{ route('admin.tracking.links') }}"
class="sidebar-link px-4 py-3 text-gray-700 flex items-center gap-3 {{ request()->routeIs('admin.fleet.*') ? 'active' : '' }}">
    Fleet Tracking
</a>
</a>

</nav>

</aside>

<!-- Main Content -->

<main class="main-content bg-gray-50">

<div class="p-8">

@yield('content')

</div>

</main>

</div>

<!-- Leaflet JS -->

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

@stack('scripts')
</body>
</html>

<!-- Floating Login Button -->
<div class="fixed bottom-6 right-6 z-50">
    <a href="{{ route('login') }}" aria-label="Login" 
       class="inline-flex items-center px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full shadow-lg transition">
        Login
    </a>
</div>