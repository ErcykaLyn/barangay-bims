<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    /**
     * Show the request form.
     */
    public function showRequestForm()
    {
        return view('auth.password-reset-request');
    }

    /**
     * Store the password reset request.
     */
    public function storeRequest(Request $request)
    {
        // ✅ UPDATED: Ginawang nullable ang 'reason' (optional na lang)
        $validated = $request->validate([
            'email'  => 'required|email|exists:residents,email',
            'reason' => 'nullable|string|max:500', 
        ]);

        // Check if there's already a pending request
        $existing = PasswordResetRequest::where('email', $validated['email'])
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            return back()->withErrors([
                'email' => 'You already have an existing request. Please wait for admin approval.',
            ])->withInput();
        }

        $resident = Resident::where('email', $validated['email'])->first();

        PasswordResetRequest::create([
            'resident_id' => $resident->id,
            'email'       => $validated['email'],
            'reason'      => $validated['reason'] ?? 'No reason provided.', // ✅ Fallback kung walang nilagay
            'status'      => 'pending',
        ]);

        Log::info('Password reset request created for: ' . $validated['email']);

        return redirect()->route('password.request.status', ['email' => $validated['email']])
            ->with('success', 'Your request has been submitted. Please wait for admin approval.');
    }

    /**
     * Show request status page.
     */
    public function showStatus(Request $request)
    {
        $requests = collect();

        if ($request->has('email')) {
            $requests = PasswordResetRequest::where('email', $request->email)
                ->latest()
                ->get();
        }

        return view('auth.password-reset-status', compact('requests'));
    }

    /**
     * Check status by email.
     */
    public function checkStatus(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:residents,email',
        ]);

        return redirect()->route('password.request.status', [
            'email' => $validated['email'],
        ]);
    }

    /**
     * Show reset password form (after approval).
     */
    public function showResetForm($request_id)
    {
        $resetRequest = PasswordResetRequest::findOrFail($request_id);

        if ($resetRequest->status !== 'approved') {
            return redirect()->route('password.request.status', ['email' => $resetRequest->email])
                ->with('error', 'Your request has not been approved yet.');
        }

        return view('auth.password-reset-form', compact('resetRequest'));
    }

    /**
     * Reset the password.
     */
    public function resetPassword(Request $request, $request_id)
    {
        $resetRequest = PasswordResetRequest::findOrFail($request_id);

        if ($resetRequest->status !== 'approved') {
            return redirect()->route('password.request.status', ['email' => $resetRequest->email])
                ->with('error', 'Your request has not been approved yet.');
        }

        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verify email matches
        if ($validated['email'] !== $resetRequest->email) {
            return back()->withErrors(['email' => 'Email does not match our records.']);
        }

        $resident = Resident::where('email', $validated['email'])->first();

        if (!$resident) {
            return back()->withErrors(['email' => 'Account not found.']);
        }

        // Update password
        $resident->password = Hash::make($validated['password']);
        $resident->save();

        // Mark as completed
        $resetRequest->status = 'completed';
        $resetRequest->completed_at = now();
        $resetRequest->save();

        Log::info('Password reset completed for: ' . $validated['email']);

        return redirect()->route('login')
            ->with('success', 'Your password has been reset successfully! You can now log in.');
    }
}