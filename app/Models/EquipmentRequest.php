<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentRequest extends Model
{
    protected $fillable = [
        'equipment_id', 'resident_id', 'quantity', 'quantity_borrowed', 'quantity_returned', 'shortage',
        'reason', 'needed_date', 'status', 'rejection_reason', 'approved_by', 'approved_at',
        'picked_up_at', 'dispensed_at', 'dispensed_by', 'returned_at', 'due_date', 'notes',
    ];

    protected $casts = [
        'needed_date' => 'date',
        'due_date' => 'date',
        'approved_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'dispensed_at' => 'datetime',
        'returned_at' => 'datetime',
        'quantity_borrowed' => 'integer',
        'quantity_returned' => 'integer',
        'shortage' => 'integer',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function dispensedBy()
    {
        return $this->belongsTo(User::class, 'dispensed_by');
    }

    public function isPending()    { return $this->status === 'pending'; }
    public function isApproved()   { return $this->status === 'approved'; }
    public function isRejected()   { return $this->status === 'rejected'; }
    public function isPickedUp()   { return $this->status === 'picked_up'; }
    public function isReturned()   { return $this->status === 'returned'; }
    public function isDispensed()  { return $this->status === 'dispensed'; }
    public function isCancelled()  { return $this->status === 'cancelled'; }
    public function isPartialReturn() { return $this->status === 'partial_return'; }

    public function getIsMedicineAttribute()
    {
        return $this->equipment && $this->equipment->category === 'medicine';
    }

    public function getIsBorrowableAttribute()
    {
        return !$this->is_medicine;
    }

    public function getIsCompletedAttribute()
    {
        return in_array($this->status, ['returned', 'dispensed', 'rejected', 'cancelled']);
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending'        => 'bg-amber-100 text-amber-700 border-amber-200',
            'approved'       => 'bg-blue-100 text-blue-700 border-blue-200',
            'rejected'       => 'bg-rose-100 text-rose-700 border-rose-200',
            'picked_up'      => 'bg-indigo-100 text-indigo-700 border-indigo-200',
            'returned'       => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'dispensed'      => 'bg-purple-100 text-purple-700 border-purple-200',
            'cancelled'      => 'bg-slate-100 text-slate-700 border-slate-200',
            'partial_return' => 'bg-amber-100 text-amber-700 border-amber-200',
            default          => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending'        => 'Pending Approval',
            'approved'       => $this->is_medicine ? 'Approved - Ready to Claim' : 'Approved - Ready for Pickup',
            'rejected'       => 'Rejected',
            'picked_up'      => 'Picked Up',
            'returned'       => 'Returned',
            'dispensed'      => 'Dispensed',
            'cancelled'      => 'Cancelled',
            'partial_return' => 'Partial Return',
            default          => ucfirst($this->status),
        };
    }

    public function getShortStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending'        => 'Pending',
            'approved'       => $this->is_medicine ? 'To Claim' : 'To Pick Up',
            'rejected'       => 'Rejected',
            'picked_up'      => 'Picked Up',
            'returned'       => 'Returned',
            'dispensed'      => 'Dispensed',
            'cancelled'      => 'Cancelled',
            'partial_return' => 'Partial Return',
            default          => ucfirst($this->status),
        };
    }
}