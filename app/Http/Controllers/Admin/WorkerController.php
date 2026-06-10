<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Worker;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class WorkerController extends Controller
{ 
 /**
     * Display a listing of workers (users with role = worker)
     */
    public function index()
    {
        $workers = User::where('role', 'worker')
            ->with('worker')
            ->latest()
            ->paginate(15);

        return view('admin.workers.index', compact('workers'));
    }

    /**
     * Show form to create a new worker
     */
    public function create()
    {
        return view('admin.workers.create');
    }

    /**
     * Store a new worker (user + worker profile)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|string|email|max:255|unique:users',
            'phone'           => 'nullable|string|max:20',
            'password'        => ['required', 'confirmed', Password::defaults()],

            'work_id'         => 'nullable|string|max:50|unique:workers,work_id',
            'nin' => [
                'required',
                'string',
                'size:11',                       // Many countries use 11 characters for NIN
                'unique:workers,nin',
                'regex:/^C[MF][0-9A-Z]{9}$/i'    // CM/CF + 9 alphanumeric characters
            ],
            'department'      => 'nullable|string|max:100',
            'position'        => 'nullable|string|max:100',
            'hire_date'       => 'nullable|date',
            'employment_type' => 'nullable|in:permanent,contract,casual,probation',
        ]);

        // ✅ Create USER
        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'phone'     => $validated['phone'] ?? null,
            'password'  => Hash::make($validated['password']),
            'role'      => 'worker',
            'is_active' => true,
            'approved'  => true,
        ]);

        // ✅ Create WORKER PROFILE
        Worker::create([
            'user_id'         => $user->id,
            'work_id'         => $validated['work_id'] ?? null,
            'nin'             => $validated['nin'] ?? null,
            'department'      => $validated['department'] ?? null,
            'position'        => $validated['position'] ?? null,
            'hire_date'       => $validated['hire_date'] ?? null,
            'employment_type' => $validated['employment_type'] ?? null,
        ]);

        return redirect()
            ->route('admin.workers.index')
            ->with('success', 'Worker created successfully.');
    }

    /**
     * Display a specific worker
     */
    public function show(User $worker)
    {
        $worker->load('worker');

        return view('admin.workers.show', compact('worker'));
    }

    /**
     * Show edit form
     */
    public function edit(User $worker)
    {
        $worker->load('worker');

        return view('admin.workers.edit', compact('worker'));
    }

    /**
     * Update worker (user + worker profile)
     */
    public function update(Request $request, User $worker)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|string|email|max:255|unique:users,email,' . $worker->id,
            'phone'           => 'nullable|string|max:20',
            'password'        => ['nullable', 'confirmed', Password::defaults()],

            'work_id'         => 'nullable|string|max:50|unique:workers,work_id,' . ($worker->worker->id ?? 'NULL'),
            'nin'             => 'nullable|string|max:20|unique:workers,nin,' . ($worker->worker->id ?? 'NULL'),
            'department'      => 'nullable|string|max:100',
            'position'        => 'nullable|string|max:100',
            'hire_date'       => 'nullable|date',
            'employment_type' => 'nullable|in:permanent,contract,casual,probation',
        ]);

        // ✅ Update USER
        $worker->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        // Update password if provided
        if (!empty($validated['password'])) {
            $worker->update([
                'password' => Hash::make($validated['password'])
            ]);
        }

        // ✅ Update or create WORKER PROFILE
        if ($worker->worker) {
            $worker->worker->update([
                'work_id'         => $validated['work_id'] ?? null,
                'nin'             => $validated['nin'] ?? null,
                'department'      => $validated['department'] ?? null,
                'position'        => $validated['position'] ?? null,
                'hire_date'       => $validated['hire_date'] ?? null,
                'employment_type' => $validated['employment_type'] ?? null,
            ]);
        } else {
            Worker::create([
                'user_id'         => $worker->id,
                'work_id'         => $validated['work_id'] ?? null,
                'nin'             => $validated['nin'] ?? null,
                'department'      => $validated['department'] ?? null,
                'position'        => $validated['position'] ?? null,
                'hire_date'       => $validated['hire_date'] ?? null,
                'employment_type' => $validated['employment_type'] ?? null,
            ]);
        }

        return redirect()
            ->route('admin.workers.index')
            ->with('success', 'Worker updated successfully.');
    }

    /**
     * Delete worker (user + worker profile)
     */
    public function destroy(User $worker)
    {
        if ($worker->worker) {
            $worker->worker->delete();
        }

        $worker->delete();

        return redirect()
            ->route('admin.workers.index')
            ->with('success', 'Worker deleted successfully.');
    }
}
