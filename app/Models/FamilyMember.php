<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\ReportsMissingFields;

class FamilyMember extends Model
{
    use HasFactory, ReportsMissingFields;

    protected $fillable = [
        'family_profile_id',
        'relation_to_head',
        'last_name',
        'first_name',
        'middle_name',
        'date_of_birth',
        'age',
        'sex',
        'civil_status',
        'education',
        'religion',
        'ethnicity',
        'is_4ps',
        'fourps_household_id',
        'is_ip',
        'is_pwd',
        'is_deceased',
        'philhealth_id',
        'philhealth_member',
        'philhealth_category',
        'medical_history',
        'medical_specify',
        'classification',
        'immunization',
        'date_of_visit',
        'lmp_date',
        'using_fp',
        'fp_method',
        'fp_status',
        'water_source',
        'toilet_facility',
        'toilet_type',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'lmp_date'      => 'date',
        'is_4ps'        => 'boolean',
        'is_ip'         => 'boolean',
        'is_pwd'        => 'boolean',
        'is_deceased'   => 'boolean',
        'using_fp'      => 'boolean',
    ];

    /** Sa FamilyMember, 'sex' ang pangalan ng column (hindi 'gender' tulad ng sa Resident). */
    protected function sexColumnName(): string
    {
        return 'sex';
    }

    public function familyProfile()
    {
        return $this->belongsTo(FamilyProfile::class);
    }

    public function getFullNameAttribute()
    {
        return trim(
            ($this->first_name ?? '') . ' ' .
            ($this->middle_name ? $this->middle_name . ' ' : '') .
            ($this->last_name ?? '')
        );
    }

    /**
     * Civil status code. Tumatanggap ng buong salita (Single) at ng code
     * na sine-save ng importer (S, M, W, SP, L, C). Ang "SP" ay hindi na nagiging "S".
     */
    public function getCivilStatusShortAttribute()
    {
        $v = $this->civil_status;
        if (!$v) return '—';

        if (in_array(strtoupper($v), ['S', 'M', 'W', 'SP', 'L', 'C', 'A', 'O'], true)) {
            return strtoupper($v);
        }

        $codes = [
            'Single'       => 'S',
            'Married'      => 'M',
            'Widowed'      => 'W',
            'Separated'    => 'SP',
            'Cohabitation' => 'C',
            'Live-in'      => 'L',
            'Annulled'     => 'A',
            'Others'       => 'O',
        ];
        return $codes[$v] ?? substr($v, 0, 1);
    }

    public function getRelationShortAttribute()
    {
        $map = [
            'Head'       => 'Head',
            'Spouse'     => 'Spouse',
            'Son'        => 'Son',
            'Daughter'   => 'Daughter',
            'Parent'     => 'Parent',
            'Sibling'    => 'Sibling',
            'Grandchild' => 'Grandchild',
            'Others'     => 'Others',
        ];
        return $map[$this->relation_to_head] ?? ($this->relation_to_head ?? '—');
    }
}