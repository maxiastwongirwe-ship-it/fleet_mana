<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Worker Portal') - FleetFlow</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        body {
            background-color: #f0f7ff; /* Light blue background */
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .apple-card {
            background: white;
            border-radius: 28px;
            box-shadow: 0 10px 30px -10px rgb(0 0 0 / 0.08);
            border: 1px solid #e5e7eb;
        }
        .map-container {
            height: 380px;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid #dbeafe;
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen">

    <!-- Apple-style Navigation -->
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
        <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-2xl">🚐</div>
                <div>
                    <span class="font-semibold text-2xl tracking-tighter">FleetFlow</span>
                    <span class="text-xs text-gray-500 block -mt-1">Worker Portal</span>
                </div>
            </div>

            <div class="flex items-center gap-8 text-sm font-medium">
                <a href="{{ route('worker.dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                <a href="{{ route('worker.transport-requests.index') }}" class="hover:text-blue-600 transition-colors">My Requests</a>
                
                <div class="flex items-center gap-4">
                    <span class="text-gray-700">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-500 hover:text-red-600 transition">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto px-6 py-10">
        @yield('content')
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @stack('scripts')
</body>
</html>