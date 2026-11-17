<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureResidentIsAlive
{
    public function handle(Request $request, Closure $next): Response
    {
        // Titingnan kung naka-login ba ang residente at kung patay na ba siya
        if (Auth::guard('resident')->check() && Auth::guard('resident')->user()->is_deceased) {
            
            // Agad siyang itatapon palabas
            Auth::guard('resident')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Ibabalik sa login page na may error message
            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been deactivated because the resident is marked as deceased.',
            ]);
        }

        return $next($request);
    }
}