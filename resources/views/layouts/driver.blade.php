<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Driver Portal') - FleetFlow</title>

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f0f9ff;
            margin: 0;
            height: 100vh;
            overflow: hidden;
        }

        .app-container {
            display: flex;
            height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
            overflow-y: auto;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
        }

        /* Main content */
        .main-content {
            flex: 1;
            min-height: 0;
            overflow-x: auto;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            background-color: #f0f9ff;
        }

        /* Scrollbars */
        .main-content::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        .main-content::-webkit-scrollbar-track {
            background: #e0f2fe;
            border-radius: 10px;
        }
        .main-content::-webkit-scrollbar-thumb {
            background: #7dd3fc;
            border-radius: 10px;
        }
        .main-content::-webkit-scrollbar-thumb:hover {
            background: #38bdf8;
        }

        .sidebar-link {
            transition: all 0.2s ease;
            border-radius: 12px;
            padding: 12px 16px;
        }

        .sidebar-link:hover {
            background: #f0f9ff;
        }

        .sidebar-link.active {
            background: rgba(14, 165, 233, 0.12);
            color: #0ea5e9;
            font-weight: 600;
        }

        .map-container {
            height: 460px;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #bae6fd;
        }
    </style>

    @yield('head')
</head>
<body>

<div class="app-container">

    <!-- Sidebar -->
    <aside class="sidebar p-6 flex flex-col">

        <!-- Logo + Title -->
        <div class="flex items-center gap-3 mb-10">
            <div class="w-11 h-11 bg-sky-500 rounded-2xl flex items-center justify-center text-white text-3xl shadow">
                🚐
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tighter">FleetFlow</h1>
                <p class="text-xs text-sky-600 font-medium -mt-1">Driver Portal</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex flex-col gap-1 flex-1">
            <a href="{{ route('driver.dashboard') }}" 
               class="sidebar-link flex items-center gap-3 text-gray-700 {{ request()->routeIs('driver.dashboard') ? 'active' : '' }}">
                <span class="text-xl">🏠</span>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('driver.trips.index') }}" 
               class="sidebar-link flex items-center gap-3 text-gray-700 {{ request()->routeIs('driver.trips.*') ? 'active' : '' }}">
                <span class="text-xl">🛣️</span>
                <span>My Trips</span>
            </a>

            <a href="{{ route('driver.breakdowns.index') }}" 
               class="sidebar-link flex items-center gap-3 text-gray-700 {{ request()->routeIs('driver.breakdowns.*') ? 'active' : '' }}">
                <span class="text-xl">⚠️</span>
                <span>Breakdowns</span>
            </a>

            <a href="{{ route('driver.fuel-requests.index') }}" 
               class="sidebar-link flex items-center gap-3 text-gray-700 {{ request()->routeIs('driver.fuel-requests.*') ? 'active' : '' }}">
                <span class="text-xl">⛽</span>
                <span>Fuel Requests</span>
            </a>
        </nav>

        <!-- Profile Section - Top Right Style (but in sidebar bottom) -->
        <div class="mt-auto pt-8 border-t border-gray-100">
            <div class="flex items-center gap-3 bg-gray-50 rounded-2xl p-4">
                <div class="w-10 h-10 bg-sky-100 text-sky-600 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0">
                    👨‍✈️
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500">Professional Driver</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="text-red-500 hover:text-red-600 text-sm font-medium px-3 py-1 rounded-xl hover:bg-red-50 transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>

    </aside>

    <!-- Main Content -->
    <main class="main-content">
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