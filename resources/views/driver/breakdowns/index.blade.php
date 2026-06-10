@extends('layouts.driver')

@section('title', 'My Breakdown Reports')

@section('content')
<div class="space-y-8">

    <div class="flex justify-between items-center">
        <h1 class="text-4xl font-semibold tracking-tight">My Breakdown Reports</h1>
        <a href="{{ route('driver.breakdowns.create') }}" 
           class="bg-red-600 hover:bg-red-700 text-white px-8 py-4 rounded-3xl font-semibold transition flex items-center gap-3">
            <span class="text-xl">+</span> Report New Breakdown
        </a>
    </div>

    @if($breakdowns->isNotEmpty())
        <div class="grid gap-6">
            @foreach($breakdowns as $breakdown)
                <a href="{{ route('driver.breakdowns.show', $breakdown) }}" 
                   class="apple-card p-8 hover:shadow-xl transition-all block">
                    <div class="flex flex-col md:flex-row justify-between gap-6">
                        <div class="flex-1">
                            <div class="flex items-center gap-4">
                                <span class="px-5 py-2 rounded-3xl text-sm font-medium {{ $breakdown->getStatusClassAttribute() }}">
                                    {{ ucfirst($breakdown->status) }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    {{ $breakdown->occurred_at->format('D, j M • H:i') }}
                                </span>
                            </div>

                            <p class="mt-5 font-medium text-lg">{{ $breakdown->vehicle->plate_number ?? 'Unknown Vehicle' }}</p>
                            <p class="text-gray-600 line-clamp-2 mt-2">{{ $breakdown->description }}</p>
                        </div>

                        <div class="text-right md:text-left">
                            <span class="inline-block px-6 py-2 rounded-3xl text-sm font-medium
                                @if($breakdown->status === 'resolved') bg-green-100 text-green-700
                                @elseif($breakdown->status === 'rejected') bg-red-100 text-red-700
                                @else bg-amber-100 text-amber-700 @endif">
                                {{ ucfirst($breakdown->severity) }}
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $breakdowns->links() }}
        </div>
    @else
        <div class="apple-card p-20 text-center">
            <p class="text-2xl text-gray-400">No breakdown reports yet</p>
            <a href="{{ route('driver.breakdowns.create') }}" 
               class="mt-8 inline-block bg-red-600 text-white px-8 py-4 rounded-3xl font-medium">
                Report First Breakdown
            </a>
        </div>
    @endif

</div>
@endsection