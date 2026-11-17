<?php

namespace App\Http\Controllers\BHW;

use App\Http\Controllers\Controller;
use App\Models\FamilyMember;
use App\Models\FamilyProfile;
use App\Models\Resident;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        // ------------------------------------------------------------
        // POPULATION (galing sa residents table, kapareho ng admin dashboard)
        // Kasama na LAHAT ng residente (buhay man o patay) para hindi bumaba
        // ang total population kapag may namarkahang deceased.
        // ------------------------------------------------------------
        // Bilang: approved at deceased, O na-claim mula sa listahan.
        $counted = fn() => Resident::where('role', 'resident')
            ->where(fn($q) => $q->whereIn('status', ['approved', 'deceased'])->orWhere('claimed_from_import', true));
            // INALIS NA NATIN YUNG ->where('is_deceased', false) DITO

        $totalResidents = $counted()->count();
        
        // Senior = 60+ base sa birthday (laging updated, hindi umaasa sa flag)
        $seniorCutoff   = now()->subYears(60)->toDateString();
        $totalSeniors   = $counted()
            ->where(fn($q) => $q->where('is_senior', true)
                                ->orWhere(fn($w) => $w->whereNotNull('date_of_birth')
                                                      ->where('date_of_birth', '<=', $seniorCutoff)))
            ->count();
        $totalPwd       = $counted()->where('is_pwd', true)->count();
        $total4ps       = $counted()->where('is_4ps', true)->count();

        $totalMale   = $counted()->whereIn('gender', ['Male', 'male', 'M', 'm'])->count();
        $totalFemale = $counted()->whereIn('gender', ['Female', 'female', 'F', 'f'])->count();

        // ------------------------------------------------------------
        // GRAPH DATA: residents per zone at per age group
        // ------------------------------------------------------------
        $zoneData = $counted()
            ->selectRaw("COALESCE(NULLIF(zone_purok, ''), 'Walang zone') as zone, COUNT(*) as total")
            ->groupBy('zone')
            ->pluck('total', 'zone')
            ->all();
        uksort($zoneData, 'strnatcasecmp');

        $ageGroups = ['0–5' => 0, '6–12' => 0, '13–17' => 0, '18–59' => 0, '60+' => 0];
        $noBirthday = 0;
        foreach ($counted()->pluck('date_of_birth') as $dob) {
            if (!$dob) { $noBirthday++; continue; }
            $age = \Carbon\Carbon::parse($dob)->age;
            if     ($age <= 5)  $ageGroups['0–5']++;
            elseif ($age <= 12) $ageGroups['6–12']++;
            elseif ($age <= 17) $ageGroups['13–17']++;
            elseif ($age <= 59) $ageGroups['18–59']++;
            else                $ageGroups['60+']++;
        }

        // ------------------------------------------------------------
        // HOUSEHOLDS & HEALTH (galing sa Family Profiling)
        // Para sa health stats, DAPAT buhay lang ang bilangin.
        // ------------------------------------------------------------
        $memberTable = (new FamilyMember)->getTable();
        $hasDeceased = Schema::hasColumn($memberTable, 'is_deceased');

        // Buhay na members lang kung may is_deceased column
        // (INAWAN NATIN ITO DAHIL PATAY NA SILA SA HEALTH STATS)
        $members = fn() => FamilyMember::query()
            ->when($hasDeceased, fn($q) => $q->where('is_deceased', false));

        // Main households lang: ang sub-households (A/B) ay nasa loob ng main household nila,
        // kapareho ng bilang sa Household Profiling page.
        $totalHouseholds = FamilyProfile::whereNull('parent_family_id')->count();
        $profiledMembers = $members()->count();

        // Children 0–5 years (edad 5 pababa, base sa birthday para laging updated)
        $sixYearsAgo     = now()->subYears(6)->toDateString();
        $totalChildren05 = $members()
            ->whereNotNull('date_of_birth')
            ->where('date_of_birth', '>', $sixYearsAgo)
            ->count();

        // Hypertension / Diabetes (Form2: "Hypertension", "Diabetes"; Consolidation: maaaring "HPN", "DM")
        // Ang maiikling code (HPN, DM) ay hinahanap lang bilang hiwalay na salita para
        // hindi tumama sa ibang salita (halimbawa "ADMITTED" na may "DM" sa loob).
        $hasCondition = function ($query, string $word, string $abbr) {
            return $query->where(function ($q) use ($word, $abbr) {
                $q->where('medical_history', 'like', "%{$word}%")
                  ->orWhere('medical_history', $abbr)
                  ->orWhere('medical_history', 'like', "{$abbr},%")
                  ->orWhere('medical_history', 'like', "{$abbr} %")
                  ->orWhere('medical_history', 'like', "%, {$abbr}")
                  ->orWhere('medical_history', 'like', "%,{$abbr}")
                  ->orWhere('medical_history', 'like', "%, {$abbr},%")
                  ->orWhere('medical_history', 'like', "%,{$abbr},%")
                  ->orWhere('medical_history', 'like', "%/{$abbr}%")
                  ->orWhere('medical_history', 'like', "% {$abbr} %");
            });
        };
        $totalHypertension = $hasCondition($members(), 'Hypertension', 'HPN')->count();
        $totalDiabetes     = $hasCondition($members(), 'Diabetes', 'DM')->count();

        // Households na walang toilet (bilang ng pamilya, hindi ng tao)
        $noToiletHouseholds = $members()
            ->where('toilet_facility', 'like', '%without toilet%')
            ->distinct()
            ->count('family_profile_id');

        // Incomplete records (walang birthday, sex, o civil status)
        $incompleteMembers = $members()
            ->where(fn($q) => $q->whereNull('date_of_birth')
                                ->orWhereNull('sex')
                                ->orWhereNull('civil_status'))
            ->count();

        return view('bhw.dashboard', compact(
            'totalResidents',
            'totalSeniors',
            'totalPwd',
            'total4ps',
            'totalMale',
            'totalFemale',
            'zoneData',
            'ageGroups',
            'noBirthday',
            'totalHouseholds',
            'profiledMembers',
            'totalChildren05',
            'totalHypertension',
            'totalDiabetes',
            'noToiletHouseholds',
            'incompleteMembers'
        ));
    }
}