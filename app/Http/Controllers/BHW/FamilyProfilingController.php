<?php

namespace App\Http\Controllers\BHW;

use App\Http\Controllers\Controller;
use App\Models\FamilyProfile;
use App\Models\FamilyMember;
use App\Models\Resident;
use App\Exports\FamilyProfilingExport;
use App\Imports\FamilyProfilingImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FamilyProfilingController extends Controller
{
    public function index(Request $request)
    {
        $query = FamilyProfile::with(['members', 'childFamilies.members', 'parentFamily']);
        $this->applyFilters($query, $request);
        $families = $query->latest()->get();
        return view('bhw.family_profiling.index', compact('families'));
    }

    public function downloadTemplate()
    {
        $fileName = 'Household_Form_1Page.xlsx';
        $filePath = storage_path('app/templates/' . $fileName);
        if (!file_exists($filePath)) {
            return back()->with('error', "Template file not found: {$fileName}. Please upload it to storage/app/templates/");
        }
        return response()->download($filePath);
    }

    public function import(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv,xlsm|max:20480']);

        try {
            $import = new FamilyProfilingImport();
            Excel::import($import, $request->file('file'));
            $message = "Import successful! {$import->families} households, {$import->imported} family members.";
            if ($import->skipped > 0) {
                $message .= " Skipped {$import->skipped} rows.";
            }
            return back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Family Profiling Import Failed: ' . $e->getMessage());
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $type = $request->query('type', 'zone');
        if ($type === 'household') {
            $family = FamilyProfile::with(['members'])->findOrFail($request->query('id'));
            $families = collect([$family]);
            $filename = 'Family_Profiling_' . Str::slug($family->family_code) . '_' . now()->format('Y-m-d_His') . '.xlsx';
        } else {
            $type = 'zone';
            $query = FamilyProfile::with(['members']);
            $this->applyFilters($query, $request);
            $families = $query->get()->sortBy('family_code', SORT_NATURAL | SORT_FLAG_CASE)->values();
            if ($families->isEmpty()) {
                return back()->with('error', 'Walang household na ie-export sa napiling zone.');
            }
            $zoneLabel = $request->filled('zone') ? Str::slug($request->zone) : 'all-zones';
            $filename = 'Family_Profiling_' . $zoneLabel . '_' . now()->format('Y-m-d_His') . '.xlsx';
        }
        return (new FamilyProfilingExport($families, $type))->download($filename);
    }

    public function create()
    {
        $zones = $this->getZones();
        return view('bhw.family_profiling.create', compact('zones'));
    }

    public function getNextCode(Request $request)
    {
        $zone = $request->query('zone');
        if (!$zone) {
            return response()->json(['code' => '']);
        }

        if (preg_match('/(\d+)\s*$/', $zone, $m)) {
            // TINANGGAL NATIN YUNG GITLING SA DULO PARA MAGING Z1-34004 + 001
            $prefix = 'Z' . (int) $m[1] . '-34004'; 

            $max = FamilyProfile::where('family_code', 'like', $prefix . '%')
                ->pluck('family_code')
                ->map(fn ($c) => preg_match('/^' . preg_quote($prefix, '/') . '(\d{3})$/', $c, $mm) ? (int) $mm[1] : 0)
                ->max() ?? 0;

            $next = $max + 1;
            
            do {
                $code = $prefix . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
                $next++;
            } while (FamilyProfile::where('family_code', $code)->exists());

            return response()->json(['code' => $code]);
        }

        return response()->json(['code' => '']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'family_code'      => 'required|string|max:255',
            'zone_purok'       => 'nullable|string',
            'complete_address' => 'nullable|string',
            'contact_number'   => 'nullable|string',
            'notes'            => 'nullable|string',
            
            'members'                          => 'required|array|min:1',
            'members.*.relation_to_head'       => 'nullable|string|max:255',
            'members.*.first_name'             => 'required_with:members|string|max:255',
            'members.*.last_name'              => 'required_with:members|string|max:255',
            'members.*.middle_name'            => 'nullable|string|max:255',
            'members.*.date_of_birth'          => 'nullable|date',
            'members.*.age'                    => 'nullable|integer|min:0|max:150',
            'members.*.sex'                    => 'nullable|in:M,F',
            'members.*.civil_status'           => 'nullable|string|max:255',
            'members.*.date_of_visit'          => 'nullable|string|max:20',

            'members.*.education'              => 'nullable|string|max:255',
            'members.*.religion'               => 'nullable|string|max:255',
            'members.*.ethnicity'              => 'nullable|string|max:255',
            'members.*.is_4ps'                 => 'nullable|boolean',
            'members.*.is_ip'                  => 'nullable|boolean',
            'members.*.is_pwd'                 => 'nullable|boolean',
            'members.*.fourps_household_id'    => 'nullable|string|max:255',
            'members.*.philhealth_id'          => 'nullable|string|max:255',
            'members.*.philhealth_member'      => 'nullable|string|max:10',
            'members.*.philhealth_category'    => 'nullable|string|max:255',
            'members.*.medical_history'        => 'nullable|string|max:255',
            'members.*.medical_specify'        => 'nullable|string|max:255',
            'members.*.classification'         => 'nullable|string|max:255',
            'members.*.immunization'           => 'nullable|string|max:255',
            'members.*.lmp_date'               => 'nullable|date',
            'members.*.using_fp'               => 'nullable|boolean',
            'members.*.fp_method'              => 'nullable|string|max:255',
            'members.*.fp_status'              => 'nullable|string|max:255',
            'members.*.water_source'           => 'nullable|string|max:255',
            'members.*.toilet_facility'        => 'nullable|string|max:255',
        ]);

        $headCount = collect($validated['members'])->where('relation_to_head', 'Head')->count();
        if ($headCount > 1) {
            return redirect()->back()->withInput()->with('error', 'Maaaring isa lang ang Head sa isang household.');
        }

        $headMember = collect($validated['members'])->firstWhere('relation_to_head', 'Head') ?? ($validated['members'][0] ?? null);
        if (!$headMember || empty($headMember['first_name']) || empty($headMember['last_name'])) {
            return redirect()->back()->withInput()->with('error', 'Kailangan may at least one member (Head) na may first name at last name.');
        }

        $baseCode = $validated['family_code'];
        $parentFamily = FamilyProfile::where('family_code', $baseCode)->first();
        $newFamilyCode = $baseCode;

        if ($parentFamily) {
            $newFamilyCode = $this->generateFamilyCode($baseCode);
        }

        $family = FamilyProfile::create([
            'family_code'      => $newFamilyCode,
            'head_first_name'  => $headMember['first_name'],
            'head_last_name'   => $headMember['last_name'],
            'zone_purok'       => $validated['zone_purok'] ?? null,
            'complete_address' => $validated['complete_address'] ?? null,
            'contact_number'   => $validated['contact_number'] ?? null,
            'notes'            => $validated['notes'] ?? null,
            'parent_family_id' => $parentFamily?->id,
        ]);

        foreach ($validated['members'] as $memberData) {
            if (empty($memberData['first_name']) && empty($memberData['last_name'])) {
                continue;
            }

            $memberData['family_profile_id'] = $family->id;
            $memberData['age'] = !empty($memberData['date_of_birth'])
                ? Carbon::parse($memberData['date_of_birth'])->age
                : ($memberData['age'] ?? null);
            
            $memberData['is_4ps'] = !empty($memberData['is_4ps']);
            $memberData['is_ip']  = !empty($memberData['is_ip']);
            $memberData['is_pwd'] = !empty($memberData['is_pwd']);
            $memberData['using_fp'] = !empty($memberData['using_fp']);
            
            $memberData['is_deceased'] = false;
            $memberData['date_of_visit'] = $memberData['date_of_visit'] ?? null;

            FamilyMember::create($memberData);
            $this->syncResidentFromMember($family, $memberData, $validated['zone_purok'] ?? null);
        }

        $message = "Household {$newFamilyCode} created successfully!";
        if ($newFamilyCode !== $baseCode) {
            $message .= " (Auto-generated from {$baseCode})";
        }

        return redirect()->route('bhw.family_profiling.show', $family->id)
            ->with('success', $message);
    }

    public function show($id)
    {
        $family = FamilyProfile::with(['members', 'childFamilies.members', 'parentFamily'])->findOrFail($id);
        return view('bhw.family_profiling.show', compact('family'));
    }

    public function edit($id)
    {
        $family = FamilyProfile::with('members')->findOrFail($id);
        $allFamilies = FamilyProfile::where('id', '!=', $id)->orderBy('head_last_name')->get();
        $zones = $this->getZones();
        return view('bhw.family_profiling.edit', compact('family', 'allFamilies', 'zones'));
    }

    public function update(Request $request, $id)
    {
        $family = FamilyProfile::findOrFail($id);

        $validated = $request->validate([
            'family_code'      => 'required|string|max:255|unique:family_profiles,family_code,' . $id,
            'zone_purok'       => 'nullable|string',
            'complete_address' => 'nullable|string',
            'contact_number'   => 'nullable|string',
            'notes'            => 'nullable|string',
            'parent_family_id' => 'nullable|exists:family_profiles,id',
            
            'members'                          => 'required|array|min:1',
            'members.*.relation_to_head'       => 'nullable|string|max:255',
            'members.*.first_name'             => 'required_with:members|string|max:255',
            'members.*.last_name'              => 'required_with:members|string|max:255',
            'members.*.middle_name'            => 'nullable|string|max:255',
            'members.*.date_of_birth'          => 'nullable|date',
            'members.*.age'                    => 'nullable|integer|min:0|max:150',
            'members.*.sex'                    => 'nullable|in:M,F',
            'members.*.civil_status'           => 'nullable|string|max:255',
            'members.*.date_of_visit'          => 'nullable|string|max:20',

            'members.*.education'              => 'nullable|string|max:255',
            'members.*.religion'               => 'nullable|string|max:255',
            'members.*.ethnicity'              => 'nullable|string|max:255',
            'members.*.is_4ps'                 => 'nullable|boolean',
            'members.*.is_ip'                  => 'nullable|boolean',
            'members.*.is_pwd'                 => 'nullable|boolean',
            'members.*.fourps_household_id'    => 'nullable|string|max:255',
            'members.*.philhealth_id'          => 'nullable|string|max:255',
            'members.*.philhealth_member'      => 'nullable|string|max:10',
            'members.*.philhealth_category'    => 'nullable|string|max:255',
            'members.*.medical_history'        => 'nullable|string|max:255',
            'members.*.medical_specify'        => 'nullable|string|max:255',
            'members.*.classification'         => 'nullable|string|max:255',
            'members.*.immunization'           => 'nullable|string|max:255',
            'members.*.lmp_date'               => 'nullable|date',
            'members.*.using_fp'               => 'nullable|boolean',
            'members.*.fp_method'              => 'nullable|string|max:255',
            'members.*.fp_status'              => 'nullable|string|max:255',
            'members.*.water_source'           => 'nullable|string|max:255',
            'members.*.toilet_facility'        => 'nullable|string|max:255',
        ]);

        $headCount = collect($validated['members'])->where('relation_to_head', 'Head')->count();
        if ($headCount > 1) {
            return redirect()->back()->withInput()->with('error', 'Maaaring isa lang ang Head sa isang household. Palitan ang iba pang members na hindi Head.');
        }

        $headMember = collect($validated['members'])->firstWhere('relation_to_head', 'Head') ?? ($validated['members'][0] ?? null);
        if (!$headMember) {
            return redirect()->back()->withInput()->with('error', 'Kailangan may at least one member (Head).');
        }

        $family->update([
            'family_code'      => $validated['family_code'],
            'head_first_name'  => $headMember['first_name'] ?? $family->head_first_name,
            'head_last_name'   => $headMember['last_name'] ?? $family->head_last_name,
            'zone_purok'       => $validated['zone_purok'] ?? null,
            'complete_address' => $validated['complete_address'] ?? null,
            'contact_number'   => $validated['contact_number'] ?? null,
            'notes'            => $validated['notes'] ?? null,
            'parent_family_id' => $validated['parent_family_id'] ?? null,
        ]);

        $family->members()->delete();

        foreach ($validated['members'] as $memberData) {
            if (empty($memberData['first_name']) && empty($memberData['last_name'])) {
                continue;
            }

            $memberData['family_profile_id'] = $family->id;
            $memberData['age'] = !empty($memberData['date_of_birth'])
                ? Carbon::parse($memberData['date_of_birth'])->age
                : ($memberData['age'] ?? null);
                
            $memberData['is_4ps'] = !empty($memberData['is_4ps']);
            $memberData['is_ip']  = !empty($memberData['is_ip']);
            $memberData['is_pwd'] = !empty($memberData['is_pwd']);
            $memberData['using_fp'] = !empty($memberData['using_fp']);
            
            $memberData['is_deceased'] = false;
            $memberData['date_of_visit'] = $memberData['date_of_visit'] ?? null;

            FamilyMember::create($memberData);
            $this->syncResidentFromMember($family, $memberData, $validated['zone_purok'] ?? null);
        }

        return redirect()->route('bhw.family_profiling.show', $family->id)
            ->with('success', 'Household profile updated successfully!');
    }

    public function destroy($id)
    {
        $family = FamilyProfile::findOrFail($id);
        
        // 1. I-delete muna yung mga Resident na naka-link sa household na 'to
        // Ito ang tama kasi ang residents table ay may family_profile_id column
        Resident::where('family_profile_id', $family->id)->delete();

        // 2. I-delete yung mga FamilyMember records
        $family->members()->delete();

        // 3. I-update yung mga child households (kung meron man)
        FamilyProfile::where('parent_family_id', $family->id)->update(['parent_family_id' => null]);
        
        // 4. I-delete na yung FamilyProfile
        $family->delete();

        return redirect()->route('bhw.family_profiling.index')->with('success', 'Household profile and its members deleted successfully!');
    }

    public function searchResidents(Request $request, FamilyProfile $family)
    {
        $query = $request->get('q');
        if (!$query) return response()->json([]);
        $residents = Resident::where('role', 'resident')
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'LIKE', "%{$query}%")
                  ->orWhere('last_name', 'LIKE', "%{$query}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$query}%"]);
            })
            ->where(function ($q) use ($family) {
                $q->whereNull('family_profile_id')->orWhere('family_profile_id', '!=', $family->id);
            })
            ->limit(10)
            ->get();
        return response()->json($residents->map(fn($r) => [
            'id' => $r->id, 'first_name' => $r->first_name, 'middle_name' => $r->middle_name,
            'last_name' => $r->last_name, 'full_name' => $r->full_name, 'zone_purok' => $r->zone_purok,
        ]));
    }

    public function attachResident(Request $request, FamilyProfile $family)
    {
        $request->validate(['resident_id' => 'required|exists:residents,id']);
        $resident = Resident::findOrFail($request->resident_id);
        if ($resident->family_profile_id && $resident->family_profile_id != $family->id) {
            return response()->json(['error' => 'Resident already belongs to another family'], 422);
        }
        $resident->update(['family_profile_id' => $family->id]);
        return response()->json([
            'success' => true,
            'resident' => ['id' => $resident->id, 'name' => $resident->full_name, 'zone' => $resident->zone_purok, 'photo' => $resident->profile_photo_url]
        ]);
    }

    public function detachResident(FamilyProfile $family, Resident $resident)
    {
        if ($resident->family_profile_id != $family->id) {
            return response()->json(['error' => 'Not a member'], 422);
        }
        $resident->update(['family_profile_id' => null]);
        return response()->json(['success' => true]);
    }

    private function generateFamilyCode(string $baseCode): string
    {
        $suffixes = range('B', 'Z');
        foreach ($suffixes as $suffix) {
            $code = $baseCode . '-' . $suffix;
            if (!FamilyProfile::where('family_code', $code)->exists()) {
                return $code;
            }
        }
        return $baseCode . '-' . strtoupper(Str::random(4));
    }

    private function syncResidentFromMember(FamilyProfile $family, array $memberData, ?string $zone): void
    {
        $resident = Resident::where('first_name', $memberData['first_name'])
            ->where('last_name', $memberData['last_name'])
            ->when(!empty($memberData['date_of_birth']), fn($q) => $q->where('date_of_birth', $memberData['date_of_birth']))
            ->first();

        if ($resident) {
            $resident->update([
                'middle_name'       => $memberData['middle_name'] ?? $resident->middle_name,
                'gender'            => ($memberData['sex'] ?? '') === 'M' ? 'Male' : (($memberData['sex'] ?? '') === 'F' ? 'Female' : $resident->gender),
                'civil_status'      => $memberData['civil_status'] ?? $resident->civil_status,
                'zone_purok'        => $zone ?? $resident->zone_purok,
                'complete_address'  => $family->complete_address ?? $resident->complete_address,
                'contact_number'    => $family->contact_number ?? $resident->contact_number,
                'family_profile_id' => $family->id,
                'is_head'           => ($memberData['relation_to_head'] ?? '') === 'Head',
            ]);
        } else {
            Resident::create([
                'first_name'        => $memberData['first_name'],
                'middle_name'       => $memberData['middle_name'] ?? null,
                'last_name'         => $memberData['last_name'],
                'date_of_birth'     => $memberData['date_of_birth'] ?? null,
                'gender'            => ($memberData['sex'] ?? '') === 'M' ? 'Male' : (($memberData['sex'] ?? '') === 'F' ? 'Female' : null),
                'civil_status'      => $memberData['civil_status'] ?? null,
                'zone_purok'        => $zone ?? null,
                'complete_address'  => $family->complete_address ?? null,
                'contact_number'    => $family->contact_number ?? null,
                'role'              => 'resident',
                'status'            => 'approved',
                'family_profile_id' => $family->id,
                'is_head'           => ($memberData['relation_to_head'] ?? '') === 'Head',
            ]);
        }
    }

    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('family_code', 'LIKE', "%{$search}%")
                  ->orWhere('head_first_name', 'LIKE', "%{$search}%")
                  ->orWhere('head_last_name', 'LIKE', "%{$search}%")
                  ->orWhereRaw("CONCAT(head_first_name, ' ', head_last_name) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("CONCAT(head_last_name, ' ', head_first_name) LIKE ?", ["%{$search}%"])
                  ->orWhereHas('members', function ($memberQuery) use ($search) {
                      $memberQuery->where('first_name', 'LIKE', "%{$search}%")
                                  ->orWhere('middle_name', 'LIKE', "%{$search}%")
                                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                                  ->orWhereRaw("CONCAT(last_name, ' ', first_name) LIKE ?", ["%{$search}%"])
                                  ->orWhereRaw("CONCAT(first_name, ' ', middle_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                  });
            });
        }
        if ($request->filled('zone')) {
            $query->where('zone_purok', $request->zone);
        }
    }

    private function getZones()
    {
        return ['Zone 1', 'Zone 2', 'Zone 3', 'Zone 4', 'Zone 5', 'Zone 6', 'Zone 7'];
    }
}