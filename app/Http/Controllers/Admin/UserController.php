<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    const PER_PAGE = 15;

    public function index(Request $request)
    {
        $users = User::query()
            ->withCount('managedHostels')
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->q . '%')
                        ->orWhere('email', 'like', '%' . $request->q . '%')
                        ->orWhere('phone', 'like', '%' . $request->q . '%');
                });
            })
            ->when(in_array($request->role, array_keys(User::ROLES)), function ($q) use ($request) {
                $q->where('role', $request->role);
            })
            ->latest()
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = User::ROLES;
        $hostels = Hostel::orderBy('name')->get();

        return view('admin.users.create', compact('roles', 'hostels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email',
            'phone' => 'nullable|string|max:191',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:' . implode(',', array_keys(User::ROLES)),
            'hostels' => 'nullable|array',
            'hostels.*' => 'exists:hostels,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        if ($user->isManager()) {
            $user->managedHostels()->sync($validated['hostels'] ?? []);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = User::ROLES;
        $hostels = Hostel::orderBy('name')->get();
        $assigned = $user->managedHostels()->pluck('hostel_id')->all();

        return view('admin.users.edit', compact('user', 'roles', 'hostels', 'assigned'));
    }

    public function update(User $user, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:191',
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:' . implode(',', array_keys(User::ROLES)),
            'hostels' => 'nullable|array',
            'hostels.*' => 'exists:hostels,id',
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
        ]);

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if ($user->isManager()) {
            $user->managedHostels()->sync($validated['hostels'] ?? []);
        } else {
            $user->managedHostels()->sync([]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user, Request $request)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}