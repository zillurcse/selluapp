<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('role') && $request->role) {
            $query->role($request->role);
        }

        $users = $query->latest()->paginate(15);
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role'     => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        return response()->json([
            'message' => 'User created successfully',
            'user'    => $user->load('roles')
        ], 201);
    }

    public function show(User $user)
    {
        return response()->json($user->load('roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'sometimes|required|string|max:255',
            'email'    => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role'     => 'sometimes|required|exists:roles,name',
        ]);

        if (isset($validated['name'])) $user->name = $validated['name'];
        if (isset($validated['email'])) $user->email = $validated['email'];
        if (isset($validated['password']) && $validated['password']) {
            $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }
        $user->save();

        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return response()->json([
            'message' => 'User updated successfully',
            'user'    => $user->load('roles')
        ]);
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user->syncRoles([$request->role]);

        return response()->json(['message' => 'Role updated successfully', 'user' => $user->load('roles')]);
    }
    
    public function destroy(User $user)
    {
        // Don't allow deleting current user or last super-admin
        if (auth()->id() === $user->id) {
            return response()->json(['message' => 'Cannot delete yourself'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
