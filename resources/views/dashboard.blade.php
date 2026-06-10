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

    @auth

        @if(!auth()->user()->role)

        <!-- WAITING FOR ROLE ASSIGNMENT -->
        <div class="max-w-3xl mx-auto mt-20 text-center">

            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-lg border border-sky-100 p-10">

                <!-- Icon -->
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-sky-100 flex items-center justify-center text-4xl">
                    ⏳
                </div>

                <h2 class="text-2xl font-semibold text-gray-800">
                    Account Pending Approval
                </h2>

                <p class="text-gray-600 mt-3">
                    Your account has been successfully created. An administrator will assign your role soon.
                </p>

                <p class="text-gray-500 text-sm mt-2">
                    You will be assigned as an <strong>Admin</strong>, <strong>Driver</strong>, or <strong>Worker</strong>.
                </p>

                <!-- Status -->
                <div class="mt-6 inline-flex items-center gap-2 px-4 py-2 bg-yellow-100 text-yellow-700 rounded-xl text-sm">
                    ⏳ Waiting for role assignment
                </div>

                <!-- Actions -->
                <div class="mt-8 flex justify-center gap-4">

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="px-5 py-2 bg-gray-100 hover:bg-gray-200 rounded-xl text-sm">
                            Logout
                        </button>
                    </form>

                    <button onclick="location.reload()" 
                            class="px-5 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-xl text-sm">
                        Refresh Status
                    </button>

                </div>

            </div>

        </div>

        @else

        <!-- USER HAS ROLE (optional fallback) -->
        <div class="max-w-4xl mx-auto text-center mt-20">
            <h2 class="text-2xl font-semibold">Redirecting...</h2>
        </div>

        @endif

    @else

    <!-- GUEST VIEW (your existing one) -->
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

    @endauth

</main>
    </div>

</div>

@livewireScripts
</body>
</html>