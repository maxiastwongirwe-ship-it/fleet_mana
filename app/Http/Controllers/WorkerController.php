<?php

namespace App\Http\Controllers;

use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class WorkerController extends Controller
{
     public function __construct()
    {
       
    }

    public function index()
{
    $workers = Worker::query()
        ->join('users', 'users.id', '=', 'workers.user_id')
        ->select('workers.*')
        ->orderBy('workers.department')
        ->orderBy('users.name')
        ->with('user')
        ->paginate(15);

    return view('workers.index', compact('workers'));
}


    public function create()
    {
        return view('workers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'unique:users,email'],
            'phone'             => ['nullable', 'string', 'max:50'],
            'password'          => ['required', 'confirmed', 'min:8'],
            'department'        => ['nullable', 'string', 'max:80'],
            'position'          => ['nullable', 'string', 'max:100'],
            'work_id'           => ['nullable', 'string', 'max:32', 'unique:workers,work_id'],
            'nin'               => ['nullable', 'string', 'max:20', 'unique:workers,nin'],
            'hire_date'         => ['nullable', 'date'],
            // add more fields as needed
        ]);

        $user = \App\Models\User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'role'     => 'worker',
            'is_active' => true,
        ]);

        $user->workerProfile()->create([
            'department' => $validated['department'],
            'position'   => $validated['position'],
            'work_id'    => $validated['work_id'],
            'nin'        => $validated['nin'],
            'hire_date'  => $validated['hire_date'],
            // add other fields
        ]);

        return redirect()->route('workers.index')
            ->with('success', 'Worker created successfully.');
    }

    public function show(Worker $worker)
    {
        $worker->load('user');
        return view('workers.show', compact('worker'));
    }

    public function edit(Worker $worker)
    {
        $worker->load('user');
        return view('workers.edit', compact('worker'));
    }

    public function update(Request $request, Worker $worker)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', Rule::unique('users')->ignore($worker->user_id)],
            'phone'      => ['nullable', 'string', 'max:50'],
            'department' => ['nullable', 'string', 'max:80'],
            'position'   => ['nullable', 'string', 'max:100'],
            'work_id'    => ['nullable', 'string', 'max:32', Rule::unique('workers')->ignore($worker->id)],
            'nin'        => ['nullable', 'string', 'max:20', Rule::unique('workers')->ignore($worker->id)],
            'hire_date'  => ['nullable', 'date'],
            'password'   => ['nullable', 'confirmed', 'min:8'],
        ]);

        $worker->user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $worker->user->phone,
        ]);

        if ($request->filled('password')) {
            $worker->user->update(['password' => \Illuminate\Support\Facades\Hash::make($validated['password'])]);
        }

        $worker->update([
            'department' => $validated['department'],
            'position'   => $validated['position'],
            'work_id'    => $validated['work_id'],
            'nin'        => $validated['nin'],
            'hire_date'  => $validated['hire_date'],
            // ...
        ]);

        return redirect()->route('workers.index')
            ->with('success', 'Worker updated successfully.');
    }

    public function destroy(Worker $worker)
    {
        // Optional: decide if you want to delete the user too
        // $worker->user->delete();   // cascades to worker because of onDelete('cascade')
        $worker->delete();

        return redirect()->route('workers.index')
            ->with('success', 'Worker deleted successfully.');
    }
}
