<x-guest-layout>
    <div class="max-w-md mx-auto p-6 bg-white rounded-2xl shadow">
        <h1 class="text-2xl font-bold text-center mb-6">Account Required</h1>

        <p class="text-center text-gray-600 mb-8">
            To start sharing your location for tracking, please log in or register an account.
        </p>

        <!-- Login Form -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-4 text-center">Already have an account? Log in</h2>
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input id="password" type="password" name="password" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                    Log In
                </button>
            </form>

            <p class="mt-4 text-center text-sm text-gray-600">
                <a href="{{ route('password.request') }}" class="text-indigo-600 hover:underline">
                    Forgot password?
                </a>
            </p>
        </div>

        <!-- Register Link -->
        <div class="text-center">
            <p class="text-gray-600 mb-2">Don't have an account yet?</p>
            <a href="{{ route('register') }}" class="inline-block px-6 py-3 bg-gray-200 text-gray-800 rounded-xl hover:bg-gray-300 transition">
                Register Now
            </a>
        </div>

        <!-- After login check message (if redirected back) -->
        @if (session('approval_message'))
            <div class="mt-8 bg-yellow-50 border border-yellow-200 text-yellow-800 px-6 py-4 rounded-xl text-center">
                {{ session('approval_message') }}
            </div>
        @endif
    </div>
</x-guest-layout>