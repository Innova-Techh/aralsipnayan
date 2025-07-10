<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class UserController extends Controller
{
// function to show homepage
// public function create()
// {
//     return view('homepage');
// }

public function store(Request $request)
{
    $validated = $request->validate([
        'username' => 'nullable|unique:users',
        'email' => 'nullable|email|unique:users',
        'password' => 'required|min:6',
        'role' => 'required|in:Student,Teacher,Admin',
    ]);

    $user = User::create([
        'username' => $validated['username'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => $validated['role'],
    ]);

    return redirect()->back()->with('success', 'User created successfully at: ' . now()->toDateTimeString());
}


}
