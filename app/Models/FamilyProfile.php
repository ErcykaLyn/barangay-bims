<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_code',
        'head_first_name',
        'head_last_name',
        'zone_purok',
        'complete_address',
        'contact_number',
        'notes',
        'parent_family_id',
    ];

    public function members()
    {
        return $this->hasMany(FamilyMember::class, 'family_profile_id');
    }

    public function livingMembers()
    {
        return $this->hasMany(FamilyMember::class, 'family_profile_id')->where('is_deceased', false);
    }

    public function deceasedMembers()
    {
        return $this->hasMany(FamilyMember::class, 'family_profile_id')->where('is_deceased', true);
    }

    public function residents()
    {
        return $this->hasMany(Resident::class, 'family_profile_id');
    }

    public function parentFamily()
    {
        return $this->belongsTo(FamilyProfile::class, 'parent_family_id');
    }

    public function childFamilies()
    {
        return $this->hasMany(FamilyProfile::class, 'parent_family_id');
    }

    public function getHeadFullNameAttribute()
    {
        return trim(($this->head_first_name ?? '') . ' ' . ($this->head_last_name ?? ''));
    }

    public function getHeadFormalNameAttribute()
    {
        return trim(($this->head_last_name ?? '') . ', ' . ($this->head_first_name ?? ''));
    }

    public function getHeadInitialsAttribute()
    {
        $first = strtoupper(substr($this->head_first_name ?? 'X', 0, 1));
        $last  = strtoupper(substr($this->head_last_name ?? 'X', 0, 1));
        return $first . $last;
    }

    public function getMemberCountAttribute()
    {
        return $this->members->count();
    }

    public function getLivingMemberCountAttribute()
    {
        return $this->members->where('is_deceased', false)->count();
    }

    public function getTotalMembersAttribute()
    {
        $direct = $this->members->count();
        $inChildren = $this->childFamilies->sum(fn($child) => $child->members->count());
        return $direct + $inChildren;
    }

    public function getSeniorCountAttribute()
    {
        return $this->members->where('age', '>=', 60)->where('is_deceased', false)->count();
    }

    public function getPwdCountAttribute()
    {
        return $this->members->where('is_pwd', true)->where('is_deceased', false)->count();
    }

    public function getFourPsCountAttribute()
    {
        return $this->members->where('is_4ps', true)->where('is_deceased', false)->count();
    }

    public function getIpCountAttribute()
    {
        return $this->members->where('is_ip', true)->where('is_deceased', false)->count();
    }

    public function getIsSubFamilyAttribute()
    {
        return !is_null($this->parent_family_id);
    }

    public function getIsMainFamilyAttribute()
    {
        return is_null($this->parent_family_id);
    }
}