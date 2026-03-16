@extends('layouts.guest')

@section('title', 'Waiting for Approval')

@section('content')
    <div class="max-w-md mx-auto mt-20 p-6 bg-white rounded-2xl shadow-lg text-center">
        <h1 class="text-2xl font-bold mb-6">Account Pending Approval</h1>

        <div class="mb-8">
            <svg class="w-20 h-20 mx-auto text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <p class="text-lg text-gray-700 mb-4">
            Your tracking account is waiting for admin approval.
        </p>
        <p class="text-gray-600">
            You'll receive a notification or can check back later.
        </p>
    </div>
@endsection