<?php

namespace App\Http\Controllers\BHW;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use Illuminate\Http\Request;

class SeniorController extends Controller
{
    public function index(Request $request)
    {
       
        $query = Resident::where('role', 'resident')->where('is_senior', true);

       
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

        $seniors = $query->latest()->get();
        
        
        $zones = Resident::where('role', 'resident')
            ->whereNotNull('zone_purok')
            ->distinct()
            ->pluck('zone_purok');

        return view('bhw.seniors.index', compact('seniors', 'zones'));
    }
}