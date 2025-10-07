<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AdminProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    /**
     * Display a listing of admins.
     */
    public function index()
    {
        return view('admin.admin.management.admin-management');
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create()
    {
        return view('admin.admin.create');
    }

    /**
     * Store a newly created admin in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'fullname' => ['required', 'string', 'max:255'],
        'username' => ['required', 'string', 'max:255', 'unique:users'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    // Split fullname into firstname and lastname
    $nameParts = explode(' ', $validated['fullname'], 2);
    $firstname = $nameParts[0];
    $lastname = $nameParts[1] ?? '';

    // Create the user
    $user = User::create([
        'username' => $validated['username'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => 'Admin',
        'status' => 'active',
    ]);

    // Create admin profile
    AdminProfile::create([
        'user_id' => $user->id,
        'firstname' => $firstname,
        'lastname' => $lastname,
    ]);

    return redirect()->route('admin.management.admins')
        ->with('success', 'Admin created successfully.');
}

    /**
     * Show the form for editing the specified admin.
     */
    public function edit(User $user)
    {
        if ($user->role !== 'Admin') {
            abort(404);
        }

        return view('admin.admin.edit', compact('user'));
    }

    /**
     * Update the specified admin in storage.
     */
    public function update(Request $request, User $user)
    {
        if ($user->role !== 'Admin') {
            abort(404);
        }

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'school_name' => ['nullable', 'string', 'max:255'],
            'grade_level_focus' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $user->update([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'status' => $validated['status'],
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->adminProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'],
                'school_name' => $validated['school_name'] ?? null,
                'grade_level_focus' => $validated['grade_level_focus'] ?? null,
            ]
        );

        return redirect()->route('admin.management.admins')
            ->with('success', 'Admin updated successfully.');
    }

    /**
     * Remove the specified admin from storage.
     */
    public function destroy(User $user)
    {
        if ($user->role !== 'Admin' || $user->id === auth()->id()) {
            return redirect()->route('admin.management.admins')
                ->with('error', 'Cannot delete this admin.');
        }

        $user->adminProfile()->delete();
        $user->delete();

        return redirect()->route('admin.management.admins')
            ->with('success', 'Admin deleted successfully.');
    }
}