@extends('layouts.admin')

@section('title', 'Vehicles')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 space-y-4 sm:space-y-0">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Vehicles</h1>
        <p class="mt-2 text-gray-600">Manage your fleet vehicles and driver assignments</p>
    </div>

    <a href="{{ route('admin.vehicles.create') }}"
       class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-xl shadow-md hover:bg-indigo-700">
        Add Vehicle
    </a>
</div>

@if(session('success'))
<div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl">
    {{ session('success') }}
</div>
@endif

<!-- HORIZONTAL SCROLL CONTAINER -->
<div class="bg-white rounded-2xl shadow border border-gray-100 overflow-x-auto">

<table class="min-w-[1200px] divide-y divide-gray-200">

<thead class="bg-gray-50">
<tr>
<th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Photo</th>
<th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Plate</th>
<th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Vehicle</th>
<th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
<th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Fuel</th>
<th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Odometer</th>
<th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Driver</th>
<th class="px-8 py-5 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
<th class="px-8 py-5 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
</tr>
</thead>

<tbody class="divide-y">

@forelse ($vehicles as $vehicle)

<tr class="hover:bg-gray-50">

<td class="px-8 py-6">

@if($vehicle->vehicle_photo_path)

<img
class="h-12 w-12 rounded object-cover"
src="{{ asset('storage/'.$vehicle->vehicle_photo_path) }}"
alt="vehicle">

@else

<div class="h-12 w-12 bg-gray-200 flex items-center justify-center text-gray-500 rounded">
V
</div>

@endif

</td>

<td class="px-8 py-6 font-medium">
{{ $vehicle->plate_number }}
</td>

<td class="px-8 py-6">
{{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->year ?? 'N/A' }})
</td>

<td class="px-8 py-6">
{{ ucfirst($vehicle->type) }}
</td>

<td class="px-8 py-6">
{{ $vehicle->fuel_type ?? 'N/A' }}
</td>

<td class="px-8 py-6">
{{ $vehicle->current_odometer }} km
</td>

<td class="px-8 py-6">

@if($vehicle->assignedDriver)

<span class="text-green-700 font-medium">
{{ $vehicle->assignedDriver->name }}
</span>

@else

<span class="text-gray-500">
Unassigned
</span>

@endif

</td>

<td class="px-8 py-6">

<span class="px-3 py-1 rounded-full text-sm
@if($vehicle->status=='active') bg-green-100 text-green-800
@elseif($vehicle->status=='maintenance') bg-yellow-100 text-yellow-800
@elseif($vehicle->status=='breakdown') bg-red-100 text-red-800
@else bg-gray-200 text-gray-800
@endif">

{{ ucfirst($vehicle->status) }}

</span>

</td>

<td class="px-8 py-6 text-right whitespace-nowrap">

<a href="{{ route('admin.vehicles.show',$vehicle) }}"
class="text-indigo-600 font-medium">View</a>

<a href="{{ route('admin.vehicles.edit',$vehicle) }}"
class="text-amber-600 ml-4 font-medium">Edit</a>

<form action="{{ route('admin.vehicles.destroy',$vehicle) }}"
method="POST"
class="inline">

@csrf
@method('DELETE')

<button class="text-red-600 ml-4 font-medium">
Delete
</button>

</form>

</td>

</tr>

@empty

<tr>
<td colspan="9" class="text-center py-12 text-gray-500">
No vehicles found
</td>
</tr>

@endforelse

</tbody>
</table>

</div>

<div class="mt-6">
{{ $vehicles->links() }}
</div>

</div>

@endsection