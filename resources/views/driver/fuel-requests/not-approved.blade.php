@extends('layouts.app')

@section('title', 'Fuel Request Not Approved')

@section('content')
<div class="max-w-4xl mx-auto py-20 px-4 sm:px-6 lg:px-8 text-center">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12">
        <h1 class="text-3xl font-semibold text-gray-900 mb-6">Not Approved Yet</h1>
        <p class="text-xl text-gray-700 mb-10">
            Your fuel request is still pending approval from admin.<br>
            You will be able to complete the fill-up and request payment once approved.
        </p>
        <a href="{{ route('driver.fuel-requests.index') }}"
           class="inline-flex items-center px-10 py-5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium shadow-md">
            Back to My Requests
        </a>
    </div>
</div>
@endsection