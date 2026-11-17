<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resident;
use App\Models\FamilyMember;
use App\Models\FamilyProfile; // <-- IDINAGDAG ITO
use App\Models\ServiceRequest;
use App\Models\Equipment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountApprovedMail;

class ResidentController extends Controller
{
    public function dashboard()
    {
        if (Auth::guard('web')->check() && Auth::guard('web')->user()->role === 'admin') {

            // Tinatanggap ang 'approved' at 'deceased' para hindi bumaba ang bilang
            $counted = fn() => Resident::where('role', 'resident')
                ->where(fn($q) => $q->whereIn('status', ['approved', 'deceased'])->orWhere('claimed_from_import', true));

            $totalResidents  = $counted()->count();

            $totalRegisteredAccounts = $counted()->whereNotNull('email')->count();

            $pendingRequests = ServiceRequest::where('status', 'pending')->count();
            $totalRequests   = ServiceRequest::count(); 

            // Senior = 60+ base sa birthday (laging updated, hindi umaasa sa flag)
            $seniorCutoff = now()->subYears(60)->toDateString();
            $totalSeniors = $counted()
                ->where(fn($q) => $q->where('is_senior', true)
                                    ->orWhere(fn($w) => $w->whereNotNull('date_of_birth')
                                                          ->where('date_of_birth', '<=', $seniorCutoff)))
                ->count();
            $total4ps     = $counted()->where('is_4ps', true)->count();
            $totalPWD     = $counted()->where('is_pwd', true)->count();
            $totalVoters  = $counted()->where('is_voter', true)->count();

            $totalMale   = $counted()->whereIn('gender', ['Male', 'male', 'M', 'm'])->count();
            $totalFemale = $counted()->whereIn('gender', ['Female', 'female', 'F', 'f'])->count();

            $pendingResidentsCount = Resident::where('status', 'pending')->count();

            $recentActivities = Resident::where('role', 'resident')
                ->latest()
                ->take(5)
                ->get();

            return view('admin.dashboard', compact(
                'totalResidents',
                'totalRegisteredAccounts',
                'pendingRequests',
                'totalRequests',
                'totalSeniors',
                'total4ps',
                'totalPWD',
                'totalVoters',
                'totalMale',
                'totalFemale',
                'recentActivities',
                'pendingResidentsCount'
            ));
        }

        if (Auth::guard('resident')->check()) {
            $equipments = Equipment::all();
            $recentRequests = ServiceRequest::where('resident_id', Auth::guard('resident')->id())
                                ->latest()
                                ->take(5)
                                ->get();

            return view('resident.dashboard', compact('equipments', 'recentRequests'));
        }

        return redirect()->route('login');
    }

    public function index(Request $request)
    {
        $query = Resident::where('role', 'resident');

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

        if ($request->filled('classification')) {
            switch ($request->classification) {
                case 'senior': $query->where('is_senior', true); break;
                case '4ps': $query->where('is_4ps', true); break;
                case 'pwd': $query->where('is_pwd', true); break;
                case 'solo_parent': $query->where('is_solo_parent', true); break;
                case 'voter': $query->where('is_voter', true); break;
                case 'deceased': $query->where('is_deceased', true); break;
                case 'incomplete': $query->needsAction(); break;
            }
        }

        $residents = $query->latest()->get();
        $zones = Resident::where('role', 'resident')
            ->whereNotNull('zone_purok')
            ->distinct()
            ->pluck('zone_purok');

        return view('admin.residents.index', compact('residents', 'zones'));
    }

    // BAGONG METHOD PARA SA DECEASED LIST
    public function deceased(Request $request)
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

        $residents = $query->latest()->get();
        $zones = Resident::where('role', 'resident')
            ->whereNotNull('zone_purok')
            ->distinct()
            ->pluck('zone_purok');

        return view('admin.residents.deceased', compact('residents', 'zones'));
    }

    public function pending()
    {
        $residents = Resident::where('status', 'pending')
            ->latest()
            ->get();

        $possibleMatches = [];
        foreach ($residents as $r) {
            if (!$r->claimed_from_import) {
                $possibleMatches[$r->id] = $r->possibleImportedMatches();
            }
        }

        return view('admin.residents.pending', compact('residents', 'possibleMatches'));
    }

    public function approve($id)
    {
        $resident = Resident::findOrFail($id);
        $resident->status = 'approved';
        $resident->save();

        if ($resident->email) {
            try {
                Mail::to($resident->email)->send(new AccountApprovedMail($resident));
            } catch (\Exception $e) {
                \Log::error('Failed to send approval email to ' . $resident->email . ': ' . $e->getMessage());
            }
        }

        return back()->with('success', "{$resident->first_name} {$resident->last_name}'s account has been APPROVED. An email notification has been sent.");
    }

    public function reject($id)
    {
        $resident = Resident::findOrFail($id);

        if ($resident->revertClaim()) {
            return back()->with('success', "{$resident->first_name} {$resident->last_name}'s registration was REJECTED. The record was returned to the list as Not registered.");
        }

        $resident->status = 'rejected';
        $resident->save();

        return back()->with('success', "{$resident->first_name} {$resident->last_name}'s account has been REJECTED.");
    }

    public function bulkApprove()
    {
        $count = Resident::where('status', 'pending')->count();

        if ($count === 0) {
            return back()->with('error', 'There are no pending accounts to approve.');
        }

        Resident::where('status', 'pending')->update(['status' => 'approved']);

        return back()->with('success', "Successfully approved {$count} pending account(s)!");
    }

    public function bulkReject()
    {
        $count = Resident::where('status', 'pending')->count();

        if ($count === 0) {
            return back()->with('error', 'There are no pending accounts to reject.');
        }

        foreach (Resident::where('status', 'pending')->get() as $r) {
            if (!$r->revertClaim()) {
                $r->status = 'rejected';
                $r->save();
            }
        }

        return back()->with('success', "Successfully rejected {$count} pending account(s).");
    }

    public function seniors(Request $request)
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

        if ($request->filled('zone')) {
            $query->where('zone_purok', $request->zone);
        }

        $residents = $query->latest()->get();
        $zones = Resident::where('role', 'resident')
            ->whereNotNull('zone_purok')
            ->distinct()
            ->pluck('zone_purok');

        return view('admin.residents.seniors', compact('residents', 'zones'));
    }

    public function pwd(Request $request)
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

        if ($request->filled('zone')) {
            $query->where('zone_purok', $request->zone);
        }

        $residents = $query->latest()->get();
        $zones = Resident::where('role', 'resident')
            ->whereNotNull('zone_purok')
            ->distinct()
            ->pluck('zone_purok');

        return view('admin.residents.pwd', compact('residents', 'zones'));
    }

    public function voters(Request $request)
    {
        $query = Resident::where('role', 'resident')->where('is_voter', true);

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

        $voters = $query->latest()->get();
        $zones = Resident::where('role', 'resident')
            ->whereNotNull('zone_purok')
            ->distinct()
            ->pluck('zone_purok');

        return view('admin.voters.index', compact('voters', 'zones'));
    }

    public function show($id)
    {
        $resident = Resident::findOrFail($id);
        return view('admin.residents.show', compact('resident'));
    }

    public function edit($id)
    {
        $resident = Resident::findOrFail($id);
        return view('admin.residents.edit', compact('resident'));
    }

    public function update(Request $request, $id)
    {
        $resident = Resident::findOrFail($id);

        $oldFirst = $resident->first_name;
        $oldLast  = $resident->last_name;

        $request->validate([
            'first_name'    => 'required|string|max:255',
            'middle_name'   => 'nullable|string|max:255',
            'last_name'     => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender'        => 'required|string',
            'civil_status'  => 'required|string',
            'zone_purok'    => 'required|string',
        ]);

        $resident->update([
            'first_name'       => $request->first_name,
            'middle_name'      => $request->middle_name,
            'last_name'        => $request->last_name,
            'date_of_birth'    => $request->date_of_birth,
            'gender'           => $request->gender,
            'civil_status'     => $request->civil_status,
            'contact_number'   => $request->contact_number,
            'occupation'       => $request->occupation,
            'zone_purok'       => $request->zone_purok,
            'complete_address' => $request->complete_address,
            'is_senior'        => $request->boolean('is_senior'),
            'is_4ps'           => $request->boolean('is_4ps'),
            'is_pwd'           => $request->boolean('is_pwd'),
            'is_solo_parent'   => $request->boolean('is_solo_parent'),
            'is_voter'         => $request->boolean('is_voter'),
        ]);

        // Panatilihing tugma ang Household Profiling (family_members) sa na-edit na resident
        $this->syncFamilyMember($resident, $oldFirst, $oldLast);

        return redirect()->route('admin.residents.index')->with('success', 'Resident updated successfully!');
    }

    public function updateAccount(Request $request, $id)
    {
        $resident = Resident::findOrFail($id);

        if (empty($resident->email)) {
            return back()->with('error', 'This resident has no account yet.');
        }

        $request->validate([
            'email'          => 'required|email|max:255|unique:residents,email,' . $resident->id,
            'password'       => 'nullable|string|min:8|confirmed',
            'contact_number' => 'nullable|string|max:50',
        ]);

        $data = [
            'email'          => $request->email,
            'contact_number' => $request->contact_number,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $resident->update($data);

        return back()->with('success', "Account of {$resident->first_name} {$resident->last_name} was updated.");
    }

    public function destroy($id)
    {
        try {
            $resident = Resident::findOrFail($id);
            $resident->delete();
            return redirect()->back()->with('success', 'Account deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Unable to delete account.');
        }
    }

    /**
     * Kapag na-edit ang resident, i-update din ang katugmang FamilyMember at FamilyProfile.
     */
    private function syncFamilyMember(Resident $resident, string $oldFirst, string $oldLast): void
    {
        if (!$resident->family_profile_id) {
            return;
        }

        // 1. I-update ang FamilyMember
        $member = FamilyMember::where('family_profile_id', $resident->family_profile_id)
            ->where('first_name', $oldFirst)
            ->where('last_name', $oldLast)
            ->first();

        if ($member) {
            $member->update([
                'first_name'    => $resident->first_name,
                'middle_name'   => $resident->middle_name,
                'last_name'     => $resident->last_name,
                'date_of_birth' => $resident->date_of_birth,
                'age'           => $resident->date_of_birth
                    ? Carbon::parse($resident->date_of_birth)->age
                    : $member->age,
                'sex'           => match ($resident->gender) {
                    'Male'   => 'M',
                    'Female' => 'F',
                    default  => $member->sex,
                },
                'civil_status'  => $resident->civil_status,
            ]);
        }

        // 2. Kung siya ang Head, i-update din ang FamilyProfile
        if ($resident->is_head) {
            $family = FamilyProfile::find($resident->family_profile_id);
            if ($family) {
                $family->update([
                    'head_first_name' => $resident->first_name,
                    'head_last_name'  => $resident->last_name,
                ]);
            }
        }
    }
}