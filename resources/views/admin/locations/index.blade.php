@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-[#f5f5f7] p-8">

    <div class="max-w-7xl mx-auto">

        <div class="flex justify-between items-center mb-8">

            <div>

                <h1 class="text-4xl font-bold text-gray-900">
                    Vehicle Tracking Links
                </h1>

                <p class="text-gray-500 mt-2">
                    Generate permanent GPS tracking links
                </p>

            </div>

            <a href="{{ route('admin.tracking.map') }}"
               class="bg-black text-white px-6 py-3 rounded-2xl">
                Open Live Map
            </a>

        </div>

        @if(session('success'))

            <div class="bg-green-100 text-green-700 px-5 py-4 rounded-2xl mb-6">

                {{ session('success') }}

            </div>

        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            @foreach($vehicles as $vehicle)

                <div class="bg-white rounded-[30px] shadow-sm p-6">

                    <h2 class="text-2xl font-bold text-gray-900">

                        {{ $vehicle->plate_number }}

                    </h2>

                    <p class="text-gray-500 mt-2">

                        {{ $vehicle->model ?? 'Vehicle' }}

                    </p>

                    <form
                        method="POST"
                        action="{{ route('admin.tracking.generate', $vehicle->id) }}"
                        class="mt-6"
                    >
                        @csrf

                        <button
                            class="w-full bg-black text-white py-3 rounded-2xl"
                        >
                            Generate Tracking Link
                        </button>

                    </form>

                    @if($vehicle->tracking_token)

                        <div class="mt-6">

                            <label class="text-sm text-gray-500">
                                Tracking Link
                            </label>

                            <input
                                type="text"
                                readonly
                                value="{{ route('device.track', $vehicle->tracking_token) }}"
                                class="w-full mt-2 bg-gray-100 rounded-2xl px-4 py-3"
                            >

                        </div>

                    @endif

                </div>

            @endforeach

        </div>

    </div>

</div>

@endsection