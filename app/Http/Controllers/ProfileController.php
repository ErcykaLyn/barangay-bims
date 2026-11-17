<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Resident;

class ProfileController extends Controller
{
    /**
     * Display the resident's profile page.
     */
    public function show()
    {
        $resident = Resident::find(Auth::guard('resident')->id());
        return view('resident.profile', compact('resident'));
    }

    /**
     * Show the form for editing the resident's profile.
     */
    public function edit()
    {
        $resident = Resident::find(Auth::guard('resident')->id());
        return view('resident.profile', compact('resident'));
    }

    /**
     * Update the resident's profile information.
     */
    public function update(Request $request)
    {
        // ✅ Gamitin ang RESIDENT guard
        $resident = Auth::guard('resident')->user();

        $validated = $request->validate([
            'first_name'       => 'required|string|max:255',
            'middle_name'      => 'nullable|string|max:255',
            'last_name'        => 'required|string|max:255',
            'date_of_birth'    => 'nullable|date|before:today',
            'gender'           => 'nullable|string|in:Male,Female,Other',
            'civil_status'     => 'nullable|string|in:Single,Married,Widowed,Separated,Divorced',
            'occupation'       => 'nullable|string|max:255',
            'contact_number'   => 'nullable|string|max:20',
            'zone_purok'       => 'nullable|string|max:100',
            'complete_address' => 'nullable|string|max:500',
            'profile_photo'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($resident->profile_photo_path && Storage::disk('public')->exists($resident->profile_photo_path)) {
                Storage::disk('public')->delete($resident->profile_photo_path);
            }

            // Store new photo sa storage/app/public/profile-photos
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $resident->profile_photo_path = $path;
        }

        // Update personal info
        $resident->first_name       = $validated['first_name'];
        $resident->middle_name      = $validated['middle_name'] ?? null;
        $resident->last_name        = $validated['last_name'];
        $resident->date_of_birth    = $validated['date_of_birth'] ?? null;
        $resident->gender           = $validated['gender'] ?? null;
        $resident->civil_status     = $validated['civil_status'] ?? null;
        $resident->occupation       = $validated['occupation'] ?? null;
        $resident->contact_number   = $validated['contact_number'] ?? null;
        $resident->zone_purok       = $validated['zone_purok'] ?? null;
        $resident->complete_address = $validated['complete_address'] ?? null;

        $resident->save();

        return redirect()->route('resident.profile')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the resident's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $resident = Auth::guard('resident')->user();

        if (!Hash::check($request->current_password, $resident->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $resident->password = Hash::make($request->password);
        $resident->save();

        return back()->with('success', 'Password changed successfully!');
    }
}