<?php

namespace App\Http\Controllers\BHW;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Models\FamilyMember;
use App\Models\FamilyProfile;
use App\Services\HouseholdService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResidentController extends Controller
{
    private const CIVIL_STATUSES = 'Single,Married,Widowed,Divorced,Separated,Cohabitation,Annulled';

    public function __construct(private HouseholdService $households)
    {
    }

    public function index(Request $request)
    {
        // INALIS NATIN YUNG ->where('is_deceased', false) PARA LUMABAS SILANG LAHAT
        $query = Resident::with('familyProfile')
            ->where('role', 'resident');

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

        if ($request->filter === 'needs_action') {
            $query->needsAction();
        } elseif ($request->filter === 'no_household') {
            $query->whereNull('family_profile_id');
        }

        // A-Z by Last Name, then First Name, then Middle Name
        // (case-insensitive at walang epekto ang extra spaces)
        // Kung gusto mo First Name muna ang basehan, i-swap lang ang first_name at last_name sa baba.
        $residents = $query
            ->orderByRaw('LOWER(TRIM(last_name)) ASC')
            ->orderByRaw('LOWER(TRIM(first_name)) ASC')
            ->orderByRaw('LOWER(TRIM(COALESCE(middle_name, ""))) ASC')
            ->get();

        return view('bhw.residents.index', compact('residents'));
    }

    public function create(Request $request)
    {
        $households = FamilyProfile::withCount(['members as living_count' => fn ($q) => $q->where('is_deceased', false)])
            ->get()
            ->sortBy('family_code', SORT_NATURAL | SORT_FLAG_CASE)
            ->map(fn ($f) => [
                'id'      => $f->id,
                'code'    => $f->family_code,
                'head'    => $f->head_formal_name,
                'zone'    => $f->zone_purok,
                'members' => $f->living_count,
            ])
            ->values();

        $selectedFamily = $request->filled('household')
            ? FamilyProfile::find($request->household)
            : null;

        return view('bhw.residents.create', compact('households', 'selectedFamily'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'      => 'required|string|max:255',
            'middle_name'     => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'date_of_birth'   => 'required|date',
            'gender'          => 'required|in:Male,Female,Other',
            'civil_status'    => 'required|in:' . self::CIVIL_STATUSES,
            'contact_number'  => 'nullable|string',
            'zone_purok'      => 'required|string',
            'complete_address'=> 'nullable|string',
            'is_senior'       => 'boolean',
            'is_pwd'          => 'boolean',
            'is_4ps'          => 'boolean',
            'is_solo_parent'  => 'boolean',
            'is_voter'        => 'boolean',

            'household_mode'    => 'required|in:none,new,existing',
            'custom_family_code'=> 'nullable|string|max:255',
            'family_profile_id' => 'required_if:household_mode,existing|nullable|exists:family_profiles,id',
            'relation_to_head'  => 'required_if:household_mode,existing|nullable|in:Spouse,Son,Daughter,Parent,Sibling,Grandchild,Others',
        ]);

        $mode = $validated['household_mode'];

        try {
            [$resident, $family] = DB::transaction(function () use ($request, $validated, $mode) {

                $resident = new Resident();
                $resident->forceFill([
                    'first_name'       => $validated['first_name'],
                    'middle_name'      => $validated['middle_name'],
                    'last_name'        => $validated['last_name'],
                    'date_of_birth'    => $validated['date_of_birth'],
                    'gender'           => $validated['gender'],
                    'civil_status'     => $validated['civil_status'],
                    'contact_number'   => $validated['contact_number'] ?? null,
                    'zone_purok'       => $validated['zone_purok'],
                    'complete_address' => $validated['complete_address'] ?? null,
                    'is_senior'        => $request->boolean('is_senior'),
                    'is_pwd'           => $request->boolean('is_pwd'),
                    'is_4ps'           => $request->boolean('is_4ps'),
                    'is_solo_parent'   => $request->boolean('is_solo_parent'),
                    'is_voter'         => $request->boolean('is_voter'),
                    'role'             => 'resident',
                    'status'           => 'approved',
                ])->save();

                $family = null;

                if ($mode === 'new') {
                    $family = $this->households->createHouseholdFor($resident, [
                        'family_code'      => $validated['custom_family_code'] ?? null,
                        'zone_purok'       => $validated['zone_purok'],
                        'complete_address' => $validated['complete_address'] ?? null,
                        'contact_number'   => $validated['contact_number'] ?? null,
                    ]);
                } elseif ($mode === 'existing') {
                    $family = FamilyProfile::findOrFail($validated['family_profile_id']);
                    $this->households->addToHousehold($resident, $family, $validated['relation_to_head']);
                }

                return [$resident, $family];
            });
        } catch (\DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $name = trim("{$resident->first_name} {$resident->last_name}");

        if ($family) {
            $message = $mode === 'new'
                ? "{$name} was added as the Head of new household {$family->family_code}. You can now add the rest of the family below."
                : "{$name} was added to household {$family->family_code} as {$validated['relation_to_head']}.";

            return redirect()
                ->route('bhw.residents.create', ['household' => $family->id])
                ->with('success', $message);
        }

        return redirect()->route('bhw.residents.index')
            ->with('success', "Resident added successfully.");
    }

    public function show($id)
    {
        $resident = Resident::findOrFail($id);
        if ($resident->role !== 'resident') {
            abort(403);
        }
        return view('bhw.residents.show', compact('resident'));
    }

    public function edit($id)
    {
        $resident = Resident::findOrFail($id);
        if ($resident->role !== 'resident') {
            abort(403, 'Only resident accounts can be edited here.');
        }
        return view('bhw.residents.edit', compact('resident'));
    }

    public function update(Request $request, $id)
    {
        $resident = Resident::findOrFail($id);
        if ($resident->role !== 'resident') {
            abort(403);
        }

        $oldFirst = $resident->first_name;
        $oldLast  = $resident->last_name;

        $validated = $request->validate([
            'first_name'      => 'required|string|max:255',
            'middle_name'     => 'nullable|string|max:255',
            'last_name'       => 'required|string|max:255',
            'date_of_birth'   => 'required|date',
            'gender'          => 'required|in:Male,Female,Other',
            'civil_status'    => 'required|in:' . self::CIVIL_STATUSES,
            'contact_number'  => 'nullable|string',
            'zone_purok'      => 'required|string',
            'complete_address'=> 'nullable|string',
            'is_senior'       => 'boolean',
            'is_pwd'          => 'boolean',
            'is_4ps'          => 'boolean',
            'is_solo_parent'  => 'boolean',
            'is_voter'        => 'boolean',
        ]);

        $data = [
            'first_name'       => $validated['first_name'],
            'middle_name'      => $validated['middle_name'] ?? null,
            'last_name'        => $validated['last_name'],
            'date_of_birth'    => $validated['date_of_birth'],
            'gender'           => $validated['gender'],
            'civil_status'     => $validated['civil_status'],
            'contact_number'   => $validated['contact_number'] ?? null,
            'zone_purok'       => $validated['zone_purok'],
            'complete_address' => $validated['complete_address'] ?? null,
            'is_senior'        => $request->boolean('is_senior'),
            'is_pwd'           => $request->boolean('is_pwd'),
            'is_4ps'           => $request->boolean('is_4ps'),
            'is_solo_parent'   => $request->boolean('is_solo_parent'),
            'is_voter'         => $request->boolean('is_voter'),
        ];

        $resident->update($data);

        // I-sync sa HouseholdService (para mag-update din sa FamilyMember at FamilyProfile)
        $this->households->syncMemberFromResident($resident, $oldFirst, $oldLast);

        return redirect()->route('bhw.residents.index')->with('success', 'Resident updated.');
    }

    public function markDeceased(Request $request, $id)
    {
        $resident = Resident::findOrFail($id);

        if ($resident->role !== 'resident') {
            abort(403);
        }

        $request->validate([
            'date_of_death'  => 'required|date|before_or_equal:today',
            'cause_of_death' => 'nullable|string|max:500',
        ]);

        $resident->update([
            'is_deceased'    => true,
            'date_of_death'  => $request->date_of_death,
            'cause_of_death' => $request->cause_of_death,
            // 'status'         => 'deceased',  <-- TINANGGAL NATIN ITO PARA HINDI BUMABA ANG COUNT
        ]);

        if ($resident->family_profile_id) {
            FamilyMember::where('family_profile_id', $resident->family_profile_id)
                ->where('first_name', $resident->first_name)
                ->where('last_name', $resident->last_name)
                ->update([
                    'is_deceased'   => true,
                    'date_of_death' => $request->date_of_death,
                ]);
        }

        return back()->with('success', "{$resident->first_name} {$resident->last_name} has been marked as deceased.");
    }

    // BAGONG FUNCTION PARA GINAWANG HEAD OF THE FAMILY ANG ISANG RESIDENT
    public function makeAsHead(Request $request, $id)
    {
        $resident = Resident::findOrFail($id);

        if ($resident->role !== 'resident') {
            abort(403);
        }

        // Siguraduhin na wala pa siyang household
        if ($resident->family_profile_id) {
            return back()->with('error', 'This resident already belongs to a household.');
        }

        try {
            DB::transaction(function () use ($resident) {
                // IPAPASA NATIN NA NULL ANG FAMILY_CODE PARA ANG HouseholdService
                // ANG MAG-AUTO-GENERATE NG CODE BASE SA ZONE (e.g., Z1-34004-001)
                $this->households->createHouseholdFor($resident, [
                    'family_code'      => null, // ITO ANG SUSI
                    'zone_purok'       => $resident->zone_purok,
                    'complete_address' => $resident->complete_address,
                    'contact_number'   => $resident->contact_number,
                ]);
            });
        } catch (\DomainException $e) {
            // Catch yung specific exception na galing sa HouseholdService
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create household: ' . $e->getMessage());
        }

        return back()->with('success', "{$resident->first_name} {$resident->last_name} is now the Head of a new household!");
    }

    public function destroy($id)
    {
        $resident = Resident::findOrFail($id);
        if ($resident->role !== 'resident') {
            abort(403);
        }
        $resident->delete();
        return redirect()->route('bhw.residents.index')->with('success', 'Resident deleted.');
    }
}