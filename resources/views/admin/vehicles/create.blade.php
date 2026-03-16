@extends('layouts.admin')

@section('title','Add Vehicle')

@section('content')

<div class="max-w-4xl mx-auto overflow-y-auto max-h-screen px-4">

<h1 class="text-3xl font-bold mb-10">Add Vehicle</h1>

<form method="POST"
action="{{ route('admin.vehicles.store') }}"
enctype="multipart/form-data"
class="bg-white rounded-2xl shadow p-10 space-y-8">

@csrf

<label class="block text-lg font-medium">Vehicle Photo</label>

<input type="file"
name="vehicle_photo"
accept="image/*"
class="block w-full border rounded-xl p-3">

<label class="block text-lg font-medium">Plate Number</label>

<input type="text"
name="plate_number"
required
class="w-full border rounded-xl p-4">

<label class="block text-lg font-medium">Make</label>

<input type="text"
name="make"
class="w-full border rounded-xl p-4">

<label class="block text-lg font-medium">Model</label>

<input type="text"
name="model"
class="w-full border rounded-xl p-4">

<label class="block text-lg font-medium">Year</label>

<input type="number"
name="year"
min="1900"
max="{{ date('Y') }}"
class="w-full border rounded-xl p-4">

<label class="block text-lg font-medium">Vehicle Type</label>

<select name="type" class="w-full border rounded-xl p-4">

<option value="cargo">Cargo</option>
<option value="passenger">Passenger</option>

</select>

<label class="block text-lg font-medium">Fuel Type</label>

<select name="fuel_type" class="w-full border rounded-xl p-4">

<option value="">Select fuel type</option>
<option value="Petrol">Petrol</option>
<option value="Diesel">Diesel</option>
<option value="Electric">Electric</option>
<option value="Hybrid">Hybrid</option>

</select>

<label class="block text-lg font-medium">Fuel Tank Capacity</label>

<input type="number"
step="0.1"
name="fuel_tank_capacity"
class="w-full border rounded-xl p-4">

<label class="block text-lg font-medium">Current Odometer</label>

<input type="number"
name="current_odometer"
class="w-full border rounded-xl p-4">

<label class="block text-lg font-medium">Capacity</label>

<input type="number"
name="capacity"
class="w-full border rounded-xl p-4">

<label class="block text-lg font-medium">Status</label>

<select name="status"
class="w-full border rounded-xl p-4">

<option value="active">Active</option>
<option value="maintenance">Maintenance</option>
<option value="breakdown">Breakdown</option>
<option value="retired">Retired</option>

</select>

<label class="block text-lg font-medium">Assign Driver</label>

<select name="assigned_driver_id"
class="w-full border rounded-xl p-4">

<option value="">Unassigned</option>

@foreach($drivers as $driver)

<option value="{{ $driver->id }}">
{{ $driver->name }}
</option>

@endforeach

</select>

<button
class="bg-indigo-600 text-white px-10 py-4 rounded-xl mt-6">

Add Vehicle

</button>

</form>

</div>

@endsection