<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name'       => 'required|string|max:255',
            'middle_name'      => 'nullable|string|max:255',
            'last_name'        => 'required|string|max:255',
            'email'            => 'required|email|max:255|unique:residents,email',
            'password'         => 'required|string|min:8|confirmed',
            'zone_purok'       => 'required|string',
            'date_of_birth'    => 'required|date',
            'gender'           => 'required|string',
            'civil_status'     => 'required|string',
            'contact_number'   => 'nullable|string',
            'occupation'       => ['nullable', 'string', 'max:255', 'not_regex:/@/'],
            'complete_address' => 'required|string',
            'profile_photo'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'id_type'          => 'required|string|max:255',
            'valid_id'         => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $photoPath = null;
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());

            $destinationPath = public_path('uploads/profile-photos');
            if (!is_dir($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $photoPath = 'uploads/profile-photos/' . $filename;
        }

        $validIdPath = null;
        if ($request->hasFile('valid_id')) {
            $file = $request->file('valid_id');
            $filename = 'valid_id_' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());

            $destinationPath = public_path('uploads/valid-ids');
            if (!is_dir($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $validIdPath = 'uploads/valid-ids/' . $filename;
        }

        // Kung nasa listahan na ang tao (na-import, walang email pa), i-CLAIM ang dating
        // record imbes na gumawa ng bago. Tugma = pangalan (maluwag: walang accent, "Ma." = "Maria")
        // at parehong birthday. Kapag wala o higit sa isa ang tumugma, gagawa ng bagong account.
        $existing = Resident::findClaimable($request->first_name, $request->last_name, $request->date_of_birth);

        if ($existing) {
            // Kunin ang bagong login/ID details; panatilihin ang datos na galing sa Excel.
            $existing->forceFill([
                'email'              => $request->email,
                'password'           => Hash::make($request->password),
                'contact_number'     => $request->contact_number ?: $existing->contact_number,
                'occupation'         => $request->occupation ?: $existing->occupation,
                'complete_address'   => $request->complete_address ?: $existing->complete_address,
                'zone_purok'         => $existing->zone_purok ?: $request->zone_purok,
                'gender'             => $existing->gender ?: $request->gender,
                'civil_status'       => $existing->civil_status ?: $request->civil_status,
                'profile_photo_path' => $photoPath ?: $existing->profile_photo_path,
                'id_type'            => $request->id_type,
                'valid_id'           => $validIdPath,
                // Hindi pwedeng mag-login hangga't hindi na-aapprove ng admin.
                'status'             => 'pending',
                'claimed_from_import' => true,
                'claimed_at'         => now(),
                'is_4ps'             => $existing->is_4ps  || $request->has('is_4ps'),
                'is_pwd'             => $existing->is_pwd  || $request->has('is_pwd'),
                'is_solo_parent'     => $existing->is_solo_parent || $request->has('is_solo_parent'),
                'is_voter'           => $existing->is_voter || $request->has('is_voter'),
            ])->save();
        } else {
            Resident::create([
                'first_name'         => $request->first_name,
                'middle_name'        => $request->middle_name,
                'last_name'          => $request->last_name,
                'email'              => $request->email,
                'password'           => Hash::make($request->password),
                'zone_purok'         => $request->zone_purok,
                'date_of_birth'      => $request->date_of_birth,
                'gender'             => $request->gender,
                'civil_status'       => $request->civil_status,
                'contact_number'     => $request->contact_number,
                'occupation'         => $request->occupation,
                'complete_address'   => $request->complete_address,
                'profile_photo_path' => $photoPath,
                'id_type'            => $request->id_type,
                'valid_id'           => $validIdPath,
                'role'               => 'resident',
                'status'             => 'pending',

                'is_senior'          => $request->has('is_senior') ? 1 : 0,
                'is_4ps'             => $request->has('is_4ps') ? 1 : 0,
                'is_pwd'             => $request->has('is_pwd') ? 1 : 0,
                'is_solo_parent'     => $request->has('is_solo_parent') ? 1 : 0,
                'is_voter'           => $request->has('is_voter') ? 1 : 0,
            ]);
        }

        return redirect()->route('login')->with('success', 'Registration successful! Please wait for admin approval before you can log in.');
    }
}