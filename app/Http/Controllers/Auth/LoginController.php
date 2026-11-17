<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the Resident login form. (Public)
     */
    public function showResidentLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Show the Admin login form. (Secret URL)
     */
    public function showAdminLoginForm()
    {
        return view('auth.login-admin');
    }

    /**
     * Show the BHW login form. (Secret URL)
     */
    public function showBhwLoginForm()
    {
        return view('auth.login-bhw');
    }

    /**
     * Handle login logic for Resident, Admin, and BHW.
     */
    public function login(Request $request)
    {
        // Validate the role sent from the login form
        $request->validate([
            'role' => 'required|in:resident,admin,bhw',
        ]);

        // ==================== ADMIN or BHW LOGIN ====================
        // Both use the 'web' guard and email field
        if ($request->role === 'admin' || $request->role === 'bhw') {
            $credentials = $request->validate([
                'email'    => 'required|email',
                'password' => 'required',
            ]);

            // Attempt login using the default 'web' guard (users table)
            if (Auth::guard('web')->attempt($credentials)) {
                $request->session()->regenerate();
                $user = Auth::user();

                // Check if the authenticated user has the correct role
                if ($user->role !== $request->role) {
                    Auth::guard('web')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    return back()->withErrors(['email' => "You are not authorized as a {$request->role}."]);
                }

                // Redirect based on role
                if ($request->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                } else {
                    return redirect()->route('bhw.dashboard');
                }
            }

            return back()->withErrors([
                'email' => 'Invalid email or password.',
            ])->onlyInput('email');
        }

        // ==================== RESIDENT LOGIN ====================
        // Resident login uses the custom 'resident' guard (residents table, email field)
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('resident')->attempt($credentials)) {
            $request->session()->regenerate();
            $resident = Auth::guard('resident')->user();

            // Check if the resident's account is approved
            if ($resident->status !== 'approved') {
                // Logout immediately since account is not approved
                Auth::guard('resident')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Return specific error message based on status
                $errorMessage = $resident->status === 'pending' 
                    ? 'Your account is still pending admin approval. Please wait.' 
                    : 'Your account registration has been rejected.';

                return back()->withErrors([
                    'email' => $errorMessage,
                ])->onlyInput('email');
            }

            // === SECURITY CHECK PARA SA DECEASED ===
            // Kahit approved ang status, hindi papapasukin kung patay na.
            if ($resident->is_deceased) {
                Auth::guard('resident')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'This account has been deactivated because the resident is marked as deceased.',
                ])->onlyInput('email');
            }
            // =======================================

            // If approved and alive, proceed to dashboard
            return redirect()->route('resident.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->onlyInput('email');
    }

    /**
     * Logout – clears both guards (web for admin/bhw, resident for residents).
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        Auth::guard('resident')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}