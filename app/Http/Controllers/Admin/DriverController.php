<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DriverController extends Controller
{
      public function __construct()
    {
        // Later: $this->middleware(['auth', 'verified', 'admin']);
    }

    /**
     * Display a listing of drivers.
     */
    public function index()
    {
        $drivers = Driver::query()
            ->join('users', 'drivers.user_id', '=', 'users.id')
            ->select('drivers.*', 'users.name', 'users.email', 'users.phone')
            ->orderBy('users.name')
            ->paginate(15);

        return view('admin.drivers.index', compact('drivers'));
    }

    /**
     * Show the form for creating a new driver profile.
     */
    public function create()
    {
        // Only users with role 'driver' who don't have a driver profile yet
        $availableUsers = User::where('role', 'driver')
            ->whereDoesntHave('driverProfile')
            ->orderBy('name')
            ->get();

        if ($availableUsers->isEmpty()) {
            return redirect()->route('admin.drivers.index')
                ->with('warning', 'No available driver users found. Create a user with "driver" role first.');
        }

        return view('admin.drivers.create', compact('availableUsers'));
    }

    /**
     * Store a newly created driver profile.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'               => [
                'required',
                'exists:users,id',
                Rule::unique('drivers', 'user_id'),
            ],
            'license_number'        => ['required', 'string', 'max:10'],
            'license_category'      => ['nullable', 'string', 'max:50'],
            'license_issue_date'    => ['nullable', 'date'],
            
            'license_expiry_date'   => ['nullable', 'date', 'after_or_equal:license_issue_date'],
            'nin_number'            => ['nullable', 'string'],
            'driver_photo'          => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'status'                => ['required', Rule::in(['active', 'suspended', 'expired'])],
        ], [
            'user_id.unique' => 'This user already has a driver profile.',
        ]);

        $data = $validated;

        // Handle driver photo upload
        if ($request->hasFile('driver_photo') && $request->file('driver_photo')->isValid()) {
            $data['driver_photo_path'] = $request->file('driver_photo')->store('driver-photos', 'public');
        }

        Driver::create($data);

        return redirect()->route('admin.drivers.index')
            ->with('success', 'Driver profile created successfully.');
    }

    /**
     * Display the specified driver profile.
     */
    public function show(Driver $driver)
    {
        $driver->load('user');

        return view('admin.drivers.show', compact('driver'));
    }

    /**
     * Show the form for editing the driver profile.
     */
    public function edit(Driver $driver)
    {
        $driver->load('user');

        return view('admin.drivers.edit', compact('driver'));
    }

    /**
     * Update the specified driver profile.
     */
    public function update(Request $request, Driver $driver)
    {
        $validated = $request->validate([
            'license_number'        => ['required', 'string', 'max:100'],
            'license_category'      => ['nullable', 'string', 'max:50'],
            'license_issue_date'    => ['nullable', 'date'],
              'approved' => 'boolean',
            'license_expiry_date'   => ['nullable', 'date', 'after_or_equal:license_issue_date'],
            'nin_number'            => ['nullable', 'string'],
            'driver_photo'          => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'status'                => ['required', Rule::in(['active', 'suspended', 'expired'])],
        ]);

        $data = $validated;

        // Handle new photo upload & old photo deletion
        if ($request->hasFile('driver_photo') && $request->file('driver_photo')->isValid()) {
            // Delete old photo if exists
            if ($driver->driver_photo_path) {
                Storage::disk('public')->delete($driver->driver_photo_path);
            }
            $data['driver_photo_path'] = $request->file('driver_photo')->store('driver-photos', 'public');
        }

        $driver->update($data);

        return redirect()->route('admin.drivers.index')
            ->with('success', 'Driver profile updated successfully.');
    }

    /**
     * Remove the specified driver profile.
     */
    public function destroy(Driver $driver)
    {
        // Delete photo if exists
        if ($driver->driver_photo_path) {
            Storage::disk('public')->delete($driver->driver_photo_path);
        }

        $driver->delete();

        return redirect()->route('admin.drivers.index')
            ->with('success', 'Driver profile deleted successfully.');
    }
}
