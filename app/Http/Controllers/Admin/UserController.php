<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display list of staff (admin + bhw).
     */
    public function index()
    {
        $users = User::whereIn('role', ['admin', 'bhw'])
            ->latest()
            ->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store new staff account.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:8|confirmed',
            'role'       => 'required|in:admin,bhw',
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Staff account created successfully!');
    }

    /**
     * Delete a staff account.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('success', 'Staff account deleted.');
    }
}