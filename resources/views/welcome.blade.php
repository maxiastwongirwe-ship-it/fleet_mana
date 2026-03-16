<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Fleet • Overview</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=SF+Pro+Display:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    body {
      font-family: 'SF Pro Display', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: #f5f5f7;
    }
    .blur-backdrop {
      backdrop-filter: blur(20px);
    }
    .card-hover {
      transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .card-hover:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px -10px rgba(0,0,0,0.12);
    }
  </style>
</head>
<body class="antialiased">

  <!-- Top Navigation (macOS / Apple-like top bar feel) -->
  <header class="fixed top-0 inset-x-0 z-50 bg-white/70 backdrop-blur-xl border-b border-gray-200/60">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <div class="flex items-center gap-10">
          <a href="#" class="text-2xl font-semibold tracking-tight text-black">Fleet</a>
          <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
            <a href="#" class="hover:text-black transition">Vehicles</a>
            <a href="#" class="hover:text-black transition">Journeys</a>
            <a href="#" class="hover:text-black transition">Fuel</a>
            <a href="#" class="hover:text-black transition">Bookings</a>
            <a href="#" class="hover:text-black transition">Reports</a>
          </nav>
        </div>
        <div class="flex items-center gap-6">
          <button class="text-sm font-medium text-gray-700 hover:text-black transition">Pascal</button>
          <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gray-400 to-gray-600 flex items-center justify-center text-white text-xs font-bold">
            P
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- Hero / Stats Section -->
  <section class="pt-28 pb-16 bg-gradient-to-b from-white to-[#f5f5f7]">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
      <h1 class="text-5xl md:text-6xl font-semibold tracking-tight text-center mb-4">
        Fleet Overview
      </h1>
      <p class="text-xl text-gray-500 text-center max-w-2xl mx-auto">
        Real-time visibility. Smarter decisions.
      </p>

      <!-- Stats Cards -->
      <div class="mt-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-3xl p-8 shadow-lg card-hover">
          <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Active Vehicles</p>
          <p class="text-5xl font-semibold mt-2">18</p>
          <p class="text-sm text-green-600 mt-1">+2 today</p>
        </div>

        <div class="bg-white rounded-3xl p-8 shadow-lg card-hover">
          <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Fuel This Month</p>
          <p class="text-5xl font-semibold mt-2">4,820 L</p>
          <p class="text-sm text-gray-600 mt-1">Avg 8.4 km/L</p>
        </div>

        <div class="bg-white rounded-3xl p-8 shadow-lg card-hover">
          <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Journeys Today</p>
          <p class="text-5xl font-semibold mt-2">42</p>
          <p class="text-sm text-blue-600 mt-1">Avg ETA 94%</p>
        </div>

        <div class="bg-white rounded-3xl p-8 shadow-lg card-hover">
          <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Maintenance Due</p>
          <p class="text-5xl font-semibold mt-2 text-red-600">3</p>
          <p class="text-sm text-gray-600 mt-1">Next in 4 days</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Vehicles Grid -->
  <section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
      <div class="flex items-center justify-between mb-10">
        <h2 class="text-3xl font-semibold tracking-tight">Your Fleet</h2>
        <button class="px-6 py-3 bg-black text-white rounded-full text-sm font-medium hover:bg-gray-800 transition">
          Add Vehicle
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Vehicle Card Example -->
        <div class="group bg-white rounded-3xl overflow-hidden shadow-xl card-hover border border-gray-100">
          <div class="h-64 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
            <!-- Placeholder for vehicle image / map snapshot -->
            <svg class="w-32 h-32 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 17h-2m2 0v-2m0 2v2m-2-2h-2m2 0v2m-6-8h.01M5 17h.01M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" />
            </svg>
          </div>
          <div class="p-8">
            <div class="flex items-center justify-between">
              <h3 class="text-2xl font-semibold">KCA 123X</h3>
              <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Active</span>
            </div>
            <p class="text-gray-500 mt-1">Toyota Hiace • Driver: John Doe</p>

            <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
              <div>
                <p class="text-gray-500">Today</p>
                <p class="font-medium">142 km</p>
              </div>
              <div>
                <p class="text-gray-500">Fuel</p>
                <p class="font-medium">38.2 L</p>
              </div>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-100">
              <button class="w-full py-4 bg-black/5 hover:bg-black/10 rounded-2xl text-sm font-medium transition">
                View Details →
              </button>
            </div>
          </div>
        </div>

        <!-- Repeat similar cards for other vehicles... -->
      </div>
    </div>
  </section>

  <!-- Footer (minimal Apple-style) -->
  <footer class="py-12 bg-white border-t border-gray-100 text-center text-sm text-gray-500">
    <p>Fleet Management System • © 2026 Ainobusingye Pascal</p>
  </footer>

  <script>
    // Optional: smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
          behavior: 'smooth'
        });
      });
    });
  </script>
</body>
</html>