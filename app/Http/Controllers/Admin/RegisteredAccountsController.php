<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use Illuminate\Http\Request;

class RegisteredAccountsController extends Controller
{
    public function index(Request $request)
    {
        // ✅ Kunin lang yung may email at approved status
        $query = Resident::where('role', 'resident')
            ->whereNotNull('email')
            ->where('status', 'approved');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'LIKE', "%{$search}%")
                  ->orWhere('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('middle_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('zone')) {
            $query->where('zone_purok', $request->zone);
        }

        $accounts = $query->latest()->get();

        $totalAccounts = Resident::whereNotNull('email')->where('status', 'approved')->count();
        $totalImported = Resident::whereNull('email')->where('status', 'approved')->count();

        $zones = Resident::where('role', 'resident')
            ->whereNotNull('zone_purok')
            ->distinct()
            ->pluck('zone_purok');

        return view('admin.registered_accounts.index', compact(
            'accounts', 'totalAccounts', 'totalImported', 'zones'
        ));
    }
}