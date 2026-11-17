<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Equipment extends Model
{
    protected $table = 'equipment';

    protected $fillable = [
        'item_name', 'photo_path', 'description',
        'category', 'quantity',
        'total_stock', 'available_stock', 'borrowed_stock', 'damaged_stock',
        'unit_price',
        'status', 'borrower_id', 'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Legacy: main borrower (unused when using request-based flow).
     */
    public function borrower()
    {
        return $this->belongsTo(Resident::class, 'borrower_id');
    }

    /**
     * Equipment transactions log.
     */
    public function transactions()
    {
        return $this->hasMany(EquipmentTransaction::class)->latest();
    }

    /**
     * ✅ NEW: Equipment has many borrow requests.
     */
    public function requests()
    {
        return $this->hasMany(EquipmentRequest::class)->latest();
    }

    /**
     * ✅ NEW: Active requests (pending, approved, picked up).
     */
    public function activeRequests()
    {
        return $this->hasMany(EquipmentRequest::class)
            ->whereIn('status', ['pending', 'approved', 'picked_up'])
            ->latest();
    }

    // ==================== ACCESSORS ====================

    /**
     * Accessor for photo URL.
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo_path && Storage::disk('public')->exists($this->photo_path)) {
            return Storage::url($this->photo_path);
        }
        return null;
    }

    /**
     * Remaining days until due.
     */
    public function getRemainingDaysAttribute()
    {
        if (!$this->due_date) return null;
        return now()->diffInDays($this->due_date, false);
    }

    /**
     * ✅ NEW: Pending requests count.
     */
    public function getPendingRequestsCountAttribute()
    {
        return $this->requests()->where('status', 'pending')->count();
    }

    /**
     * ✅ NEW: Total stock in different states.
     */
    public function getStockSummaryAttribute()
    {
        return [
            'total'     => $this->total_stock,
            'available' => $this->available_stock,
            'borrowed'  => $this->borrowed_stock,
            'damaged'   => $this->damaged_stock,
        ];
    }

    // ==================== BUSINESS LOGIC ====================

    /**
     * Borrow stock from this equipment (used internally).
     */
    public function borrow($quantity, $residentId, $dueDate, $notes = null)
    {
        if ($this->available_stock < $quantity) {
            throw new \Exception('Not enough available stock.');
        }

        $this->decrement('available_stock', $quantity);
        $this->increment('borrowed_stock', $quantity);

        // Update main status
        if ($this->available_stock <= 0) {
            $this->status = 'borrowed';
            $this->borrower_id = $residentId;
            $this->due_date = $dueDate;
            $this->save();
        }

        return $this->transactions()->create([
            'resident_id' => $residentId,
            'type' => 'borrow',
            'quantity' => $quantity,
            'due_date' => $dueDate,
            'notes' => $notes,
        ]);
    }

    /**
     * Return stock.
     */
    public function returnStock($quantity, $residentId = null, $notes = null)
    {
        $this->increment('available_stock', $quantity);
        $this->decrement('borrowed_stock', $quantity);

        if ($this->available_stock > 0) {
            $this->status = 'available';
            $this->borrower_id = null;
            $this->due_date = null;
        }
        $this->save();

        return $this->transactions()->create([
            'resident_id' => $residentId,
            'type' => 'return',
            'quantity' => $quantity,
            'returned_at' => now(),
            'notes' => $notes,
        ]);
    }

    /**
     * Mark stock as damaged.
     */
    public function markDamaged($quantity, $notes = null)
    {
        $this->decrement('available_stock', $quantity);
        $this->increment('damaged_stock', $quantity);
        $this->save();

        return $this->transactions()->create([
            'type' => 'damage',
            'quantity' => $quantity,
            'notes' => $notes,
        ]);
    }

    /**
     * Restock / add inventory.
     */
    public function restock($quantity, $notes = null)
    {
        $this->increment('total_stock', $quantity);
        $this->increment('available_stock', $quantity);
        $this->increment('quantity', $quantity);
        $this->save();

        return $this->transactions()->create([
            'type' => 'restock',
            'quantity' => $quantity,
            'notes' => $notes,
        ]);
    }
}