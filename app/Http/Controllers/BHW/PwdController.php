<?php

namespace App\Http\Controllers\BHW;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use Illuminate\Http\Request;

class PwdController extends Controller
{
    public function index(Request $request)
    {
        
        $query = Resident::where('role', 'resident')->where('is_pwd', true);

       
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('middle_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%");
            });
        }

        // Filter by zone
        if ($request->filled('zone')) {
            $query->where('zone_purok', $request->zone);
        }

        $pwds = $query->latest()->get();
        
       
        $zones = Resident::where('role', 'resident')
            ->whereNotNull('zone_purok')
            ->distinct()
            ->pluck('zone_purok');

        return view('bhw.pwds.index', compact('pwds', 'zones'));
    }
}