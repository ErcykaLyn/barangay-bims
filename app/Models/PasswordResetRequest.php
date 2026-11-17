<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'email',
        'reason',
        'status',
        'admin_notes',
        'approved_by',
        'approved_at',
        'rejected_at',
        'completed_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Relationship: Request belongs to a Resident.
     */
    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    /**
     * Relationship: Request approved by a User (Admin).
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope: Pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}