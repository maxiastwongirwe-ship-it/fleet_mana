<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
   

    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::orderBy('name')
            ->with('driverProfile')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'phone'             => ['nullable', 'string', 'max:50'],
            'password'          => ['required', 'confirmed', Password::defaults()],
            'role'              => ['required', 'in:driver,admin_level_1,admin_level_2,worker'],
            'is_active'         => ['boolean'],
            'photo'             => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $data = [
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'phone'      => $validated['phone'] ?? null,
            'password'   => Hash::make($validated['password']),
            'role'       => $validated['role'],
            'is_active'  => $request->boolean('is_active', true),
        ];

        // Handle profile photo upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile-photos', 'public');
            $data['profile_photo_path'] = $path;
        }

        $user = User::create($data);

        // Auto-create driver profile if role is driver
        if ($user->role === 'driver') {
            $user->driverProfile()->create([]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load('driverProfile');

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the user.
     */
    public function edit(User $user)
    {
        // Extra protection: Level 1 cannot edit Level 2
        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have permission to edit this user.');
        }

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        // Level 1 cannot edit Level 2
        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have permission to edit this user.');
        }

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', "unique:users,email,{$user->id}"],
            'phone'    => ['nullable', 'string', 'max:50'],
            'role'     => ['required', 'in:driver,admin_level_1,admin_level_2,worker'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'is_active'=> ['boolean'],
            'photo'    => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $data = [
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'phone'     => $validated['phone'] ?? null,
            'role'      => $validated['role'],
            'is_active' => $request->boolean('is_active', $user->is_active),
        ];

        // Update password only if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        // Handle photo update
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $data['profile_photo_path'] = $request->file('photo')->store('profile-photos', 'public');
        }

        $user->update($data);

        // Handle driver profile
        if ($user->role === 'driver') {
            if (!$user->driverProfile) {
                $user->driverProfile()->create([]);
            }
        } else {
            // Remove driver profile if role changed
            if ($user->driverProfile) {
                $user->driverProfile()->delete();
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the user.
     */
    public function destroy(User $user)
    {
        // Cannot delete self or other super admin (unless you are super admin)
        if ($user->id === auth()->id() || ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin())) {
            abort(403, 'You cannot delete this user.');
        }

        // Clean up photo
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function manageRoles()
{
    $users = User::whereNotIn('role', ['admin_level_1'])->get(); // exclude super admins
    return view('admin.users.manage-roles', compact('users'));
}

public function updateRole(Request $request, User $user)
{
    $validated = $request->validate([
        'role' => 'required|in:driver,worker,admin_level_1,admin_level_2,pending',
        'approved' => 'boolean',
    ]);

    // Only super admins can promote to admin_level_1 or 2
    if (in_array($validated['role'], ['admin_level_1', 'admin_level_2']) && auth()->user()->role !== 'admin_level_1') {
        abort(403);
    }

    $user->update([
        'role' => $validated['role'],
        'approved' => $validated['approved'] ?? false,
    ]);

    return back()->with('success', 'User role updated successfully.');
}
}
