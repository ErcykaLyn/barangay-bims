<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'resident_id',
        'document_type',
        'purpose',
        'business_name',
        'business_owner',
        'business_address',
        'application_type',
        'ctc_no',
        'valid_id',
        'id_ref_no',
        'notes',
        'status',
        'requested_at',
        'processed_at',
        // ✅ Bagong workflow columns
        'accepted_at',
        'denied_at',
        'ready_at',
        'completed_at',
        'denial_reason',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        // ✅ Bagong casts
        'accepted_at'  => 'datetime',
        'denied_at'    => 'datetime',
        'ready_at'     => 'datetime',
        'completed_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * The resident who made this request.
     */
    public function resident()
    {
        return $this->belongsTo(Resident::class, 'resident_id');
    }

    // ==================== ACCESSORS ====================

    /**
     * Human-readable status label.
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending'          => 'Pending',
            'accepted'         => 'Accepted',
            'ready_for_pickup' => 'Ready for Pickup',
            'completed'        => 'Completed',
            'denied'           => 'Denied',
            default            => ucfirst($this->status),
        };
    }

    /**
     * Status color class for badges.
     */
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending'          => 'bg-amber-50 text-amber-700 border-amber-200',
            'accepted'         => 'bg-sky-50 text-sky-700 border-sky-200',
            'ready_for_pickup' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'completed'        => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'denied'           => 'bg-rose-50 text-rose-700 border-rose-200',
            default            => 'bg-slate-50 text-slate-600 border-slate-200',
        };
    }

    /**
     * Status icon (FontAwesome class).
     */
    public function getStatusIconAttribute()
    {
        return match ($this->status) {
            'pending'          => 'fa-clock',
            'accepted'         => 'fa-check',
            'ready_for_pickup' => 'fa-box-open',
            'completed'        => 'fa-check-double',
            'denied'           => 'fa-times-circle',
            default            => 'fa-inbox',
        };
    }

    /**
     * Formatted reference number (e.g., REQ-0001).
     */
    public function getReferenceNumberAttribute()
    {
        return 'REQ-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * ✅ Check kung locked na (completed na) ang request.
     */
    public function getIsLockedAttribute()
    {
        return $this->status === 'completed';
    }

    /**
     * ✅ Check kung editable pa ang request.
     */
    public function getIsEditableAttribute()
    {
        return $this->status !== 'completed';
    }

    /**
     * ✅ Check kung archivable na (completed o denied).
     */
    public function getIsArchivedAttribute()
    {
        return in_array($this->status, ['completed', 'denied']);
    }

    /**
     * ✅ Check kung active pa (pending, accepted, ready).
     */
    public function getIsActiveAttribute()
    {
        return in_array($this->status, ['pending', 'accepted', 'ready_for_pickup']);
    }

    /**
     * ✅ Check kung ready for pickup.
     */
    public function getIsReadyAttribute()
    {
        return $this->status === 'ready_for_pickup';
    }

    /**
     * ✅ Check kung can be accepted (pending lang).
     */
    public function getCanBeAcceptedAttribute()
    {
        return $this->status === 'pending';
    }

    /**
     * ✅ Check kung can be denied (hindi pa completed).
     */
    public function getCanBeDeniedAttribute()
    {
        return $this->status !== 'completed';
    }

    /**
     * ✅ Check kung can be deleted (accepted, completed, denied).
     */
    public function getCanBeDeletedAttribute()
    {
        return in_array($this->status, ['accepted', 'completed', 'denied']);
    }

    // ==================== SCOPES ====================

    /**
     * Only pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * ✅ Only accepted requests.
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Only ready for pickup.
     */
    public function scopeReadyForPickup($query)
    {
        return $query->where('status', 'ready_for_pickup');
    }

    /**
     * Only completed requests.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Only denied requests.
     */
    public function scopeDenied($query)
    {
        return $query->where('status', 'denied');
    }

    /**
     * Active requests (pending, accepted, ready for pickup).
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'accepted', 'ready_for_pickup']);
    }

    /**
     * ✅ Only editable requests (hindi pa completed).
     */
    public function scopeEditable($query)
    {
        return $query->where('status', '!=', 'completed');
    }

    /**
     * ✅ Only locked requests (completed na).
     */
    public function scopeLocked($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * ✅ Only archived requests (completed o denied).
     */
    public function scopeArchived($query)
    {
        return $query->whereIn('status', ['completed', 'denied']);
    }
}