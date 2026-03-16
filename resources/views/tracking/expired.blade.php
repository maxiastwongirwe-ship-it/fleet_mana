<x-guest-layout>
    <div class="max-w-md mx-auto p-6 bg-white rounded-2xl shadow text-center">
        <h1 class="text-2xl font-bold mb-4">Link Expired</h1>
        <p class="text-gray-600 mb-8">
            This tracking link has expired. Please request a new one from your admin.
        </p>
        <a href="{{ url('/') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">
            Back to Home
        </a>
    </div>
</x-guest-layout>