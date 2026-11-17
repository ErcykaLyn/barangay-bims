<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResidentRegistry extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'gender',
        'civil_status',
        'zone_purok',
        'complete_address',
        'is_claimed',
        'resident_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_claimed' => 'boolean',
    ];

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }
}