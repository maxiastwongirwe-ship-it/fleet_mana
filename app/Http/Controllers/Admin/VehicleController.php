<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{

  public function __construct()
    {
    
    }

    public function index()
    {
        $vehicles = Vehicle::with('assignedDriver')
            ->orderBy('plate_number')
            ->paginate(15);

        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $drivers = User::where('role', 'driver')->orderBy('name')->get();

        return view('admin.vehicles.create', compact('drivers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number'          => ['required', 'string', 'max:50', 'unique:vehicles'],
            'make'                  => ['nullable', 'string', 'max:100'],
            'model'                 => ['nullable', 'string', 'max:100'],
            'year'                  => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
            'type'                  => ['required', 'in:cargo,passenger'],
            'capacity'              => ['nullable', 'integer', 'min:1'],
            'fuel_type'             => ['nullable', 'string', 'max:50'],
            'fuel_tank_capacity'    => ['nullable', 'numeric', 'min:0'],
            'current_odometer'      => ['nullable', 'integer', 'min:0'],
            'vehicle_photo'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:4096'],
            'assigned_driver_id'    => ['nullable', 'exists:users,id'],
            'status'                => ['required', 'in:active,maintenance,breakdown,retired'],
        ]);

        $data = $validated;

        // Handle photo upload
        if ($request->hasFile('vehicle_photo')) {
            $data['vehicle_photo_path'] = $request->file('vehicle_photo')->store('vehicle-photos', 'public');
        }

        Vehicle::create($data);

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle added successfully.');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load('assignedDriver');

        return view('admin.vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        $vehicle->load('assignedDriver');
        $drivers = User::where('role', 'driver')->orderBy('name')->get();

        return view('admin.vehicles.edit', compact('vehicle', 'drivers'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'plate_number'          => ['required', 'string', 'max:50', "unique:vehicles,plate_number,{$vehicle->id}"],
            'make'                  => ['nullable', 'string', 'max:100'],
            'model'                 => ['nullable', 'string', 'max:100'],
            'year'                  => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
            'type'                  => ['required', 'in:cargo,passenger'],
            'capacity'              => ['nullable', 'integer', 'min:1'],
            'fuel_type'             => ['nullable', 'string', 'max:50'],
            'fuel_tank_capacity'    => ['nullable', 'numeric', 'min:0'],
            'current_odometer'      => ['nullable', 'integer', 'min:' . ($vehicle->current_odometer ?? 0)], // cannot decrease odometer
            'vehicle_photo'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:4096'],
            'assigned_driver_id'    => ['nullable', 'exists:users,id'],
            'status'                => ['required', 'in:active,maintenance,breakdown,retired'],
        ]);

        $data = $validated;

        // Handle photo
        if ($request->hasFile('vehicle_photo')) {
            if ($vehicle->vehicle_photo_path) {
                Storage::disk('public')->delete($vehicle->vehicle_photo_path);
            }
            $data['vehicle_photo_path'] = $request->file('vehicle_photo')->store('vehicle-photos', 'public');
        }

        $vehicle->update($data);

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->vehicle_photo_path) {
            Storage::disk('public')->delete($vehicle->vehicle_photo_path);
        }

        $vehicle->delete();

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle deleted successfully.');
    }

    /**
 * Assign a driver to a vehicle
 */
/**
 * Assign a driver to a vehicle – with check that driver isn't already assigned elsewhere
 */
public function assignDriver(Request $request, Vehicle $vehicle)
{
    $validated = $request->validate([
        'driver_id' => 'required|exists:users,id,role,driver',
    ]);

    $driver = User::findOrFail($validated['driver_id']);

    // Check if this driver is already assigned to ANY other vehicle
    $existingAssignment = Vehicle::where('assigned_driver_id', $driver->id)
                                 ->where('id', '!=', $vehicle->id) // exclude current vehicle
                                 ->first();

    if ($existingAssignment) {
        return back()->with('error', "Driver {$driver->name} is already assigned to vehicle {$existingAssignment->plate_number}. A driver can only be assigned to one active vehicle at a time.");
    }

    // If no conflict, assign
    $vehicle->assigned_driver_id = $driver->id;
    $vehicle->save();

    return back()->with('success', "Driver {$driver->name} successfully assigned to vehicle {$vehicle->plate_number}.");
}
}
