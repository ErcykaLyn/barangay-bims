<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentTransaction extends Model
{
    protected $fillable = [
        'equipment_id', 'resident_id', 'user_id',
        'type', 'quantity', 'notes', 'due_date', 'returned_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'returned_at' => 'date',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}