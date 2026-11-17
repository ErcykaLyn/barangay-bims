<?php

namespace App\Http\Controllers\BHW;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use Illuminate\Http\Request;

class DeceasedController extends Controller
{
    public function index(Request $request)
    {
        $query = Resident::where('role', 'resident')->where('is_deceased', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('middle_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('zone')) {
            $query->where('zone_purok', $request->zone);
        }

        $deceased = $query->latest('date_of_death')->get();

        $zones = Resident::where('role', 'resident')
            ->whereNotNull('zone_purok')
            ->distinct()
            ->pluck('zone_purok');

        return view('bhw.residents.deceased', compact('deceased', 'zones'));
    }
}