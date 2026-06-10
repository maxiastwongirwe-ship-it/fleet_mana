@extends('layouts.guest')

@section('title', 'FleetFlow - Smart Fleet Management')

@section('content')
<div class="min-h-screen">

    <!-- Hero Section -->
    <div class="relative bg-gradient-to-br from-sky-700 via-blue-600 to-indigo-700 text-white overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(at_top_right,#ffffff10_0%,transparent_50%)]"></div>
        
        <div class="max-w-7xl mx-auto px-6 pt-24 pb-32 relative z-10">
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div>
                    <h1 class="text-6xl md:text-7xl font-semibold tracking-tighter leading-none mb-6">
                        FleetFlow
                    </h1>
                    <p class="text-2xl md:text-3xl text-sky-100 mb-8">
                        Smart fleet management for Uganda
                    </p>
                    <p class="text-xl text-sky-200 max-w-lg mb-10">
                        Real-time GPS tracking • Fuel management • Maintenance • Secure driver location sharing
                    </p>

                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('login') }}" 
                           class="px-10 py-4 bg-white text-sky-700 font-semibold rounded-2xl hover:bg-sky-100 transition text-lg shadow-lg">
                            Login to Dashboard
                        </a>
                        <a href="{{ route('register') }}" 
                           class="px-10 py-4 border-2 border-white/80 hover:bg-white/10 font-semibold rounded-2xl transition text-lg">
                            Create Account
                        </a>
                    </div>

                    <div class="mt-12 flex items-center gap-8 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                            <span>Live in Uganda</span>
                        </div>
                        <div>Trusted by transport companies</div>
                    </div>
                </div>

                <!-- Map Section -->
                <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-white/30 h-96 md:h-[460px]">
                    <div id="uganda-map" class="w-full h-full"></div>
                    
                    <!-- Overlay label -->
                    <div class="absolute top-6 left-6 bg-white/90 backdrop-blur-md px-5 py-3 rounded-2xl shadow text-gray-900 font-medium">
                        📍 Kampala, Uganda
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="max-w-7xl mx-auto px-6 py-24">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-semibold text-gray-900 mb-4">Everything your fleet needs</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Built for Ugandan transport companies — from boda boda fleets to heavy trucks.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white rounded-3xl p-10 shadow hover:shadow-xl transition">
                <div class="text-5xl mb-6">📍</div>
                <h3 class="text-2xl font-semibold mb-4">Real-time GPS Tracking</h3>
                <p class="text-gray-600">Share secure, time-limited tracking links with drivers. View live location on map.</p>
            </div>

            <div class="bg-white rounded-3xl p-10 shadow hover:shadow-xl transition">
                <div class="text-5xl mb-6">⛽</div>
                <h3 class="text-2xl font-semibold mb-4">Fuel Management</h3>
                <p class="text-gray-600">Drivers request fuel. Admins approve. Upload receipts and track consumption per vehicle.</p>
            </div>

            <div class="bg-white rounded-3xl p-10 shadow hover:shadow-xl transition">
                <div class="text-5xl mb-6">🔧</div>
                <h3 class="text-2xl font-semibold mb-4">Maintenance & Breakdowns</h3>
                <p class="text-gray-600">Log repairs, track odometer readings, and manage vehicle documents easily.</p>
            </div>
        </div>
    </div>

    <!-- CTA Footer -->
    <div class="bg-sky-900 text-white py-20">
        <div class="max-w-4xl mx-auto text-center px-6">
            <h2 class="text-4xl font-semibold mb-6">Ready to manage your fleet smarter?</h2>
            <p class="text-xl text-sky-200 mb-10">Join hundreds of Ugandan transport operators using FleetFlow.</p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" 
                   class="px-10 py-5 bg-white text-sky-900 font-semibold rounded-2xl text-lg hover:bg-sky-100 transition">
                    Get Started Free
                </a>
                <a href="{{ route('login') }}" 
                   class="px-10 py-5 border-2 border-white/70 hover:bg-white/10 font-semibold rounded-2xl text-lg transition">
                    Login to Your Account
                </a>
            </div>
        </div>
    </div>

</div>

<!-- Leaflet Map Script -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Uganda coordinates (centered on Kampala)
        const map = L.map('uganda-map').setView([0.3476, 32.5825], 10);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 18,
        }).addTo(map);

        // Add a marker in Kampala
        L.marker([0.3476, 32.5825]).addTo(map)
            .bindPopup('<b>Kampala</b><br>FleetFlow Operations')
            .openPopup();

        // Optional: Add a few more points
        L.marker([0.3167, 32.5667]).addTo(map)
            .bindPopup('Entebbe International Airport');
    });
</script>

@endsection