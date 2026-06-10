<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{

  /**
     * Check if driver is already assigned to another vehicle
     */
    private function driverIsAlreadyAssigned($driverId, $excludeVehicleId = null)
    {
        $query = Vehicle::where('assigned_driver_id', $driverId);

        if ($excludeVehicleId) {
            $query->where('id', '!=', $excludeVehicleId);
        }

        return $query->exists();
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
            'plate_number' => [
                'required',
                'string',
                'max:12',
                'unique:vehicles,plate_number',
                'regex:/^U[A-Z]{1,2}\s\d{3}\s[A-Z]{1,2}$/i'
            ],
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

        // === NEW: Prevent duplicate driver assignment ===
        if (!empty($validated['assigned_driver_id'])) {
            if ($this->driverIsAlreadyAssigned($validated['assigned_driver_id'])) {
                return back()
                    ->withInput()
                    ->with('error', 'This driver is already assigned to another vehicle.');
            }
        }

        $data = $validated;

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
        'plate_number' => [
            'required',
            'string',
            'max:12',
            'unique:vehicles,plate_number,' . $vehicle->id,
            'regex:/^U[A-Z]{1,2}\s\d{3}\s[A-Z]{1,2}$/i'
        ],
        'make'                  => ['nullable', 'string', 'max:100'],
        'model'                 => ['nullable', 'string', 'max:100'],
        'year'                  => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
        'type'                  => ['required', 'in:cargo,passenger'],
        'capacity'              => ['nullable', 'integer', 'min:1'],
        'fuel_type'             => ['nullable', 'string', 'max:50'],
        'fuel_tank_capacity'    => ['nullable', 'numeric', 'min:0'],
        'current_odometer'      => ['nullable', 'integer', 'min:' . ($vehicle->current_odometer ?? 0)],
        'vehicle_photo'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:4096'],
        'assigned_driver_id'    => ['nullable', 'exists:users,id'],
        'status'                => ['required', 'in:active,maintenance,breakdown,retired'],
    ]);

    // === Prevent duplicate driver assignment ===
    if (!empty($validated['assigned_driver_id']) && $validated['assigned_driver_id'] != $vehicle->assigned_driver_id) {
        if ($this->driverIsAlreadyAssigned($validated['assigned_driver_id'], $vehicle->id)) {
            return back()
                ->withInput()
                ->with('error', 'This driver is already assigned to another vehicle.');
        }
    }

    $data = $validated;

    try {
        if ($request->hasFile('vehicle_photo')) {
            if ($vehicle->vehicle_photo_path) {
                Storage::disk('public')->delete($vehicle->vehicle_photo_path);
            }
            $data['vehicle_photo_path'] = $request->file('vehicle_photo')->store('vehicle-photos', 'public');
        }

        $vehicle->update($data);

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle updated successfully.');

    } catch (\Exception $e) {
        // Catch the exception thrown from Vehicle model boot()
        return back()
            ->withInput()
            ->with('error', $e->getMessage());
    }
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
     * Dedicated Assign Driver Action (with strong check)
     */
    public function assignDriver(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:users,id,role,driver',
        ]);

        $driver = User::findOrFail($validated['driver_id']);

        if ($this->driverIsAlreadyAssigned($driver->id, $vehicle->id)) {
            return back()->with('error', "Driver {$driver->name} is already assigned to another vehicle.");
        }

        $vehicle->assigned_driver_id = $driver->id;
        $vehicle->save();

        return back()->with('success', "Driver {$driver->name} successfully assigned to {$vehicle->plate_number}.");
    }
}
