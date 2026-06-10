<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FleetFlow') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="bg-sky-50 font-sans text-gray-900 antialiased">

<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <div class="hidden md:flex md:flex-shrink-0">
        <div class="flex flex-col w-64 bg-white/80 backdrop-blur-xl border-r border-sky-100">

            <div class="flex items-center justify-center h-16 border-b border-sky-100">
                <h1 class="text-xl font-semibold text-sky-600">
                    {{ config('app.name', 'FleetFlow') }}
                </h1>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="/" class="flex items-center px-4 py-3 text-sm font-medium bg-sky-100 rounded-xl">
    🏠 Dashboard
</a>

                <a href="#" class="flex items-center px-4 py-3 text-sm text-gray-600 hover:bg-sky-50 rounded-xl">
                    🚗 Vehicles
                </a>

                <a href="#" class="flex items-center px-4 py-3 text-sm text-gray-600 hover:bg-sky-50 rounded-xl">
                    📍 Tracking
                </a>

                <a href="#" class="flex items-center px-4 py-3 text-sm text-gray-600 hover:bg-sky-50 rounded-xl">
                    📊 Reports
                </a>
            </nav>

            <div class="p-4 border-t border-sky-100">
                <p class="text-sm text-gray-500">Guest Mode</p>
            </div>

        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Top Navbar -->
        <header class="bg-white/70 backdrop-blur-xl border-b border-sky-100 h-16 flex items-center px-6 justify-between">

            <h1 class="text-lg font-semibold">
                Guest Preview
            </h1>

            <div class="flex items-center gap-4">

                <!-- Login -->
                <a href="{{ route('login') }}"
                   class="px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-xl text-sm">
                    Login
                </a>

                <!-- Register -->
                <a href="{{ route('register') }}"
                   class="px-4 py-2 bg-white border border-sky-200 hover:bg-sky-50 rounded-xl text-sm">
                    Register
                </a>

            </div>
        </header>

        <!-- CONTENT AREA -->
        <main class="flex-1 overflow-auto p-6 bg-gradient-to-br from-sky-50 via-white to-sky-100">

            <!-- If a page provides content, show it -->
            @isset($slot)
                {{ $slot }}
            @else
                <!-- Default Guest Dashboard -->
                <div class="max-w-6xl mx-auto">

                    <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-lg border border-sky-100 p-8 mb-8">
                        <h2 class="text-2xl font-semibold">Welcome 👋</h2>
                        <p class="text-gray-600 mt-2">
                            Explore the fleet management system as a guest. Login to unlock full features.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                        <div class="bg-white/80 p-6 rounded-2xl shadow border border-sky-100">
                            <p class="text-sm text-gray-500">Vehicles</p>
                            <p class="text-3xl font-semibold mt-2">120</p>
                        </div>

                        <div class="bg-white/80 p-6 rounded-2xl shadow border border-sky-100">
                            <p class="text-sm text-gray-500">Drivers</p>
                            <p class="text-3xl font-semibold mt-2">45</p>
                        </div>

                        <div class="bg-white/80 p-6 rounded-2xl shadow border border-sky-100">
                            <p class="text-sm text-gray-500">Trips</p>
                            <p class="text-3xl font-semibold mt-2">320</p>
                        </div>

                        <div class="bg-white/80 p-6 rounded-2xl shadow border border-sky-100">
                            <p class="text-sm text-gray-500">Fuel Usage</p>
                            <p class="text-3xl font-semibold mt-2">1,200L</p>
                        </div>

                    </div>

                </div>
            @endisset

        </main>
    </div>

</div>

@livewireScripts
</body>
</html>

<!-- Floating Login Button -->
<div class="fixed bottom-6 right-6 z-50">
    <a href="{{ route('login') }}" aria-label="Login" 
       class="inline-flex items-center px-4 py-3 bg-sky-600 hover:bg-sky-700 text-white rounded-full shadow-lg transition">
        Login
    </a>
</div>