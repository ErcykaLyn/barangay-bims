<?php

namespace App\Services;

use App\Models\FamilyMember;
use App\Models\FamilyProfile;
use App\Models\Resident;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HouseholdService
{
    public const CODE_BASE = '34004';

    // ------------------------------------------------------------------
    // HOUSEHOLD NUMBER
    // ------------------------------------------------------------------

    public function nextFamilyCode(?string $zone): string
    {
        if ($zone && preg_match('/(\d+)\s*$/', $zone, $m)) {
            $prefix = 'Z' . (int) $m[1] . '-' . self::CODE_BASE;

            $max = FamilyProfile::where('family_code', 'like', $prefix . '%')
                ->pluck('family_code')
                ->map(fn ($c) => preg_match('/^' . preg_quote($prefix, '/') . '(\d{3})$/', $c, $mm) ? (int) $mm[1] : 0)
                ->max() ?? 0;

            $next = $max + 1;
            do {
                $code = $prefix . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
                $next++;
            } while (FamilyProfile::where('family_code', $code)->exists());

            return $code;
        }

        do {
            $code = 'FAM-' . strtoupper(Str::random(6));
        } while (FamilyProfile::where('family_code', $code)->exists());

        return $code;
    }

    // ------------------------------------------------------------------
    // ADD RESIDENT -> HOUSEHOLD
    // ------------------------------------------------------------------

    public function createHouseholdFor(Resident $head, array $data = []): FamilyProfile
    {
        if ($head->family_profile_id) {
            throw new \DomainException('Nasa isang household na ang residenteng ito.');
        }

        return DB::transaction(function () use ($head, $data) {
            $zone = $data['zone_purok'] ?? $head->zone_purok;

            // I-check kung may ipinasang custom code
            $customCode = $data['family_code'] ?? null;
            
            // Kung may custom code, siguraduhing wala pang gumagamit. Kung meron na, mag-generate ng bago.
            if ($customCode && FamilyProfile::where('family_code', $customCode)->exists()) {
                $familyCode = $this->nextFamilyCode($zone);
            } else {
                $familyCode = $customCode ?? $this->nextFamilyCode($zone);
            }

            $family = FamilyProfile::create([
                'family_code'      => $familyCode,
                'head_first_name'  => $head->first_name,
                'head_last_name'   => $head->last_name,
                'zone_purok'       => $zone,
                'complete_address' => $data['complete_address'] ?? $head->complete_address,
                'contact_number'   => $data['contact_number'] ?? $head->contact_number,
                'notes'            => $data['notes'] ?? null,
                'parent_family_id' => null,
            ]);

            $this->makeMember($family, $head, 'Head');
            $head->update([
                'family_profile_id' => $family->id,
                'is_head'           => true,
            ]);

            return $family;
        });
    }

    public function addToHousehold(Resident $resident, FamilyProfile $family, string $relation): FamilyMember
    {
        if ($resident->family_profile_id && (int) $resident->family_profile_id !== (int) $family->id) {
            throw new \DomainException('Nasa ibang household na ang residenteng ito.');
        }

        if ($relation === 'Head' && $family->members()->where('relation_to_head', 'Head')->exists()) {
            throw new \DomainException('May Head na ang household na ito.');
        }

        return DB::transaction(function () use ($resident, $family, $relation) {
            $member = $this->makeMember($family, $resident, $relation);

            // Force update zone_purok, complete_address, contact_number mula sa household
            $resident->update([
                'family_profile_id' => $family->id,
                'zone_purok'        => $family->zone_purok ?? $resident->zone_purok,
                'complete_address'  => $family->complete_address ?? $resident->complete_address,
                'contact_number'    => $family->contact_number ?? $resident->contact_number,
                'is_head'           => $relation === 'Head',
            ]);

            return $member;
        });
    }

    public function syncMemberFromResident(Resident $resident, string $oldFirst, string $oldLast): void
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
            $sex = strtoupper(substr((string) $resident->gender, 0, 1));

            $member->update([
                'first_name'    => $resident->first_name,
                'middle_name'   => $resident->middle_name,
                'last_name'     => $resident->last_name,
                'date_of_birth' => $resident->date_of_birth,
                'age'           => $resident->date_of_birth
                    ? Carbon::parse($resident->date_of_birth)->age
                    : $member->age,
                'sex'           => in_array($sex, ['M', 'F'], true) ? $sex : $member->sex,
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

    protected function makeMember(FamilyProfile $family, Resident $r, ?string $relation): FamilyMember
    {
        $sex = strtoupper(substr((string) $r->gender, 0, 1));

        $member = FamilyMember::firstOrCreate(
            [
                'family_profile_id' => $family->id,
                'first_name'        => $r->first_name,
                'last_name'         => $r->last_name,
                'date_of_birth'     => $r->date_of_birth,
            ],
            [
                'middle_name'      => $r->middle_name,
                'relation_to_head' => $relation,
                'age'              => $r->date_of_birth ? Carbon::parse($r->date_of_birth)->age : null,
                'sex'              => in_array($sex, ['M', 'F'], true) ? $sex : null,
                'civil_status'     => $r->civil_status,
                'is_4ps'           => (bool) $r->is_4ps,
                'is_ip'            => false,
                'is_pwd'           => (bool) $r->is_pwd,
                'is_deceased'      => (bool) $r->is_deceased,
                'resident_id'      => $r->id,
            ]
        );

        if (!$member->resident_id) {
            $member->update(['resident_id' => $r->id]);
        }

        return $member;
    }

    // ------------------------------------------------------------------
    // HOUSEHOLD MEMBERS -> RESIDENTS LIST
    // ------------------------------------------------------------------

    public function syncMembersToResidents(FamilyProfile $family, bool $dryRun = false): array
    {
        $counts = ['created' => 0, 'linked' => 0, 'filled' => 0, 'skipped' => 0];

        foreach ($family->members()->get() as $m) {
            if (trim((string) $m->first_name) === '' || trim((string) $m->last_name) === '') {
                continue;
            }

            $r = $this->findResidentForMember($family, $m);

            if (!$r) {
                if (!$dryRun) {
                    $this->createResidentFromMember($family, $m);
                }
                $counts['created']++;
                continue;
            }

            if ($r->family_profile_id && (int) $r->family_profile_id !== (int) $family->id) {
                Log::info("HouseholdService: {$m->first_name} {$m->last_name} already belongs to household #{$r->family_profile_id}, skipped for #{$family->id}");
                $counts['skipped']++;
                continue;
            }

            // Force update zone_purok, complete_address, contact_number mula sa household
            $fill = [
                'family_profile_id' => $family->id,
                'zone_purok'        => $family->zone_purok ?? $r->zone_purok,
                'complete_address'  => $family->complete_address ?? $r->complete_address,
                'contact_number'    => $family->contact_number ?? $r->contact_number,
                'is_head'           => $m->relation_to_head === 'Head',
            ];

            if (!$r->date_of_birth && $m->date_of_birth) {
                $fill['date_of_birth'] = $m->date_of_birth;
            }
            if (!$r->gender && $this->sexToGender($m->sex)) {
                $fill['gender'] = $this->sexToGender($m->sex);
            }
            if (!$r->civil_status && $this->civilToWord($m->civil_status)) {
                $fill['civil_status'] = $this->civilToWord($m->civil_status);
            }

            if (!$dryRun) {
                $r->update($fill);
            }

            if (!$m->resident_id && !$dryRun) {
                $m->update(['resident_id' => $r->id]);
            }

            $counts['linked']++;
            if (count($fill) > 1) {
                $counts['filled']++;
            }
        }

        return $counts;
    }

    public function syncAll(bool $dryRun = false): array
    {
        $total = ['created' => 0, 'linked' => 0, 'filled' => 0, 'skipped' => 0, 'households' => 0];

        FamilyProfile::chunkById(100, function ($families) use (&$total, $dryRun) {
            foreach ($families as $family) {
                $c = $this->syncMembersToResidents($family, $dryRun);
                foreach ($c as $k => $v) {
                    $total[$k] += $v;
                }
                $total['households']++;
            }
        });

        return $total;
    }

    protected function findResidentForMember(FamilyProfile $family, FamilyMember $m): ?Resident
    {
        $first = mb_strtolower(trim((string) $m->first_name));
        $last  = mb_strtolower(trim((string) $m->last_name));

        $base = fn () => Resident::where('role', 'resident')
            ->whereRaw('LOWER(first_name) = ?', [$first])
            ->whereRaw('LOWER(last_name) = ?', [$last]);

        $r = $base()->where('family_profile_id', $family->id)->first();
        if ($r) {
            return $r;
        }

        if ($m->date_of_birth) {
            $r = $base()->whereDate('date_of_birth', $m->date_of_birth)->first();
            if ($r) {
                return $r;
            }
        }

        $candidates = $base()
            ->where(fn ($q) => $q->whereNull('family_profile_id')->orWhere('family_profile_id', $family->id))
            ->where(function ($q) use ($m) {
                $q->whereNull('date_of_birth');
                if (!$m->date_of_birth) {
                    $q->orWhereNotNull('date_of_birth');
                }
            })
            ->get();

        return $candidates->count() === 1 ? $candidates->first() : null;
    }

    protected function createResidentFromMember(FamilyProfile $family, FamilyMember $m): Resident
    {
        $r = new Resident();

        $r->forceFill([
            'first_name'        => $m->first_name,
            'middle_name'       => $m->middle_name,
            'last_name'         => $m->last_name,
            'date_of_birth'     => $m->date_of_birth,
            'gender'            => $this->sexToGender($m->sex),
            'civil_status'      => $this->civilToWord($m->civil_status),
            'zone_purok'        => $family->zone_purok,
            'complete_address'  => $family->complete_address,
            'contact_number'    => $m->relation_to_head === 'Head' ? $family->contact_number : null,
            'role'              => 'resident',
            'status'            => 'approved',
            'is_4ps'            => (bool) $m->is_4ps,
            'is_pwd'            => (bool) $m->is_pwd,
            'is_deceased'       => (bool) $m->is_deceased,
            'family_profile_id' => $family->id,
            'is_head'           => $m->relation_to_head === 'Head',
        ]);
        $r->save();

        if (!$m->resident_id) {
            $m->update(['resident_id' => $r->id]);
        }

        return $r;
    }

    // ------------------------------------------------------------------
    // HELPERS
    // ------------------------------------------------------------------

    protected function sexToGender(?string $sex): ?string
    {
        return match (strtoupper(substr((string) $sex, 0, 1))) {
            'M'     => 'Male',
            'F'     => 'Female',
            default => null,
        };
    }

    protected function civilToWord(?string $value): ?string
    {
        $v = trim((string) $value);
        if ($v === '') {
            return null;
        }

        $map = [
            'S'  => 'Single',    'M' => 'Married',   'W' => 'Widowed',
            'SP' => 'Separated', 'L' => 'Cohabitation', 'C' => 'Cohabitation',
            'A'  => 'Annulled',
        ];

        return $map[strtoupper($v)] ?? $v;
    }
}