<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\ReportsMissingFields;
use App\Models\ServiceRequest;
use App\Models\FamilyProfile;
use App\Models\EquipmentRequest;
use Illuminate\Support\Facades\Storage;

class Resident extends Authenticatable
{
    use HasFactory, ReportsMissingFields;

    protected $guard = 'resident';

    protected $fillable = [
        'email',
        'first_name',
        'middle_name',
        'last_name',
        'password',
        'status',
        'date_of_birth',
        'gender',
        'civil_status',
        'occupation',
        'contact_number',
        'zone_purok',
        'complete_address',
        'profile_photo_path',
        'valid_id',
        'id_type',
        'is_senior',
        'is_4ps',
        'is_pwd',
        'is_solo_parent',
        'is_voter',
        'is_deceased',
        'date_of_death',
        'cause_of_death',
        'last_viewed_status_at',
        'family_profile_id',
        'is_head', // <-- IDINAGDAG
        'claimed_from_import',
        'claimed_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'last_viewed_status_at' => 'datetime',
        'is_senior' => 'boolean',
        'is_4ps' => 'boolean',
        'is_pwd' => 'boolean',
        'is_solo_parent' => 'boolean',
        'is_voter' => 'boolean',
        'is_deceased' => 'boolean',
        'date_of_death' => 'date',
        'is_head' => 'boolean', // <-- IDINAGDAG
        'claimed_from_import' => 'boolean',
        'claimed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function (Resident $resident) {
            if ($resident->date_of_birth) {
                $resident->is_senior = $resident->date_of_birth->age >= 60;
            }
        });
    }

    public static function normalizeName(?string $name): string
    {
        $s = \Illuminate\Support\Str::ascii(mb_strtolower(trim((string) $name)));
        $s = preg_replace('/\bma\.?(?=\s|$)/', 'maria', $s);
        $s = preg_replace('/\b(jr|sr|ii|iii|iv)\b\.?/', '', $s);
        return preg_replace('/[^a-z0-9]/', '', $s);
    }

    public static function findClaimable(string $first, string $last, $dob): ?self
    {
        $f = static::normalizeName($first);
        $l = static::normalizeName($last);

        $matches = static::where('role', 'resident')
            ->whereNull('email')
            ->whereDate('date_of_birth', $dob)
            ->get()
            ->filter(fn($r) => static::normalizeName($r->first_name) === $f
                            && static::normalizeName($r->last_name) === $l);

        return $matches->count() === 1 ? $matches->first() : null;
    }

    public function possibleImportedMatches(int $limit = 3)
    {
        $f = static::normalizeName($this->first_name);
        $l = static::normalizeName($this->last_name);

        return static::where('role', 'resident')
            ->whereNull('email')
            ->where('id', '!=', $this->id)
            ->where(function ($q) {
                $q->whereRaw('LOWER(last_name) = ?', [mb_strtolower(trim($this->last_name))]);
                if ($this->date_of_birth) {
                    $q->orWhereDate('date_of_birth', $this->date_of_birth);
                }
            })
            ->get()
            ->filter(function ($r) use ($f, $l) {
                $rf = static::normalizeName($r->first_name);
                $rl = static::normalizeName($r->last_name);
                $sameDob = $this->date_of_birth && $r->date_of_birth && $this->date_of_birth->isSameDay($r->date_of_birth);
                $lastClose  = $rl === $l || levenshtein($rl, $l) <= 1;
                $firstClose = $rf === $f || levenshtein($rf, $f) <= 2
                              || ($f !== '' && $rf !== '' && (str_starts_with($rf, $f) || str_starts_with($f, $rf)));
                return ($lastClose && $firstClose) || ($sameDob && ($lastClose || $firstClose));
            })
            ->take($limit)
            ->values();
    }

    public function revertClaim(): bool
    {
        if (!$this->claimed_from_import) {
            return false;
        }

        $this->forceFill([
            'email'               => null,
            'password'            => null,
            'id_type'             => null,
            'valid_id'            => null,
            'profile_photo_path'  => null,
            'claimed_from_import' => false,
            'claimed_at'          => null,
            'status'              => 'approved',
        ])->save();

        return true;
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'resident_id');
    }

    public function equipmentRequests()
    {
        return $this->hasMany(EquipmentRequest::class, 'resident_id')->latest();
    }

    public function activeEquipmentRequests()
    {
        return $this->hasMany(EquipmentRequest::class, 'resident_id')
            ->whereIn('status', ['pending', 'approved', 'picked_up'])
            ->latest();
    }

    public function familyProfile()
    {
        return $this->belongsTo(FamilyProfile::class, 'family_profile_id');
    }

    public function readAnnouncements()
    {
        return $this->belongsToMany(Announcement::class, 'announcement_user', 'user_id', 'announcement_id')
                    ->withPivot('read_at')
                    ->withTimestamps();
    }

    public function passwordResetRequests()
    {
        return $this->hasMany(PasswordResetRequest::class, 'resident_id')->latest();
    }

    public function markAnnouncementsAsRead()
    {
        $announcementIds = Announcement::pluck('id')->toArray();
        $syncData = [];
        foreach ($announcementIds as $id) {
            $syncData[$id] = ['read_at' => now()];
        }
        $this->readAnnouncements()->syncWithoutDetaching($syncData);
    }

    public function getUnreadAnnouncementsCount()
    {
        return Announcement::whereNotIn('id', function ($query) {
            $query->select('announcement_id')
                  ->from('announcement_user')
                  ->where('user_id', $this->id);
        })->count();
    }

    public function getFullNameAttribute()
    {
        return trim(
            $this->first_name . ' ' .
            ($this->middle_name ? $this->middle_name . ' ' : '') .
            $this->last_name
        );
    }

    public function getFormalNameAttribute()
    {
        $middleInitial = $this->middle_name
            ? ' ' . strtoupper(substr($this->middle_name, 0, 1)) . '.'
            : '';

        return $this->last_name . ', ' . $this->first_name . $middleInitial;
    }

    public function getInitialsAttribute()
    {
        $initials = strtoupper(substr($this->first_name, 0, 1));

        if ($this->middle_name) {
            $initials .= strtoupper(substr($this->middle_name, 0, 1));
        }

        $initials .= strtoupper(substr($this->last_name, 0, 1));

        return $initials;
    }

    public function getProfilePhotoUrlAttribute()
    {
        if (!$this->profile_photo_path) {
            return null;
        }

        $basename = basename($this->profile_photo_path);

        $uploadPath = public_path('uploads/profile-photos/' . $basename);
        if (file_exists($uploadPath)) {
            return asset('uploads/profile-photos/' . $basename);
        }

        $storagePath = storage_path('app/public/profile-photos/' . $basename);
        if (file_exists($storagePath)) {
            return asset('storage/profile-photos/' . $basename);
        }

        if (str_starts_with($this->profile_photo_path, 'uploads/')) {
            $uploadPath = public_path($this->profile_photo_path);
            if (file_exists($uploadPath)) {
                return asset($this->profile_photo_path);
            }
        }

        $storagePath = storage_path('app/public/' . $this->profile_photo_path);
        if (file_exists($storagePath)) {
            return asset('storage/' . $this->profile_photo_path);
        }

        return asset('uploads/profile-photos/' . $basename);
    }

    public function getValidIdUrlAttribute()
    {
        if (!$this->valid_id) {
            return null;
        }

        $basename = basename($this->valid_id);

        $uploadPath = public_path('uploads/valid-ids/' . $basename);
        if (file_exists($uploadPath)) {
            return asset('uploads/valid-ids/' . $basename);
        }

        $storagePath = storage_path('app/public/valid-ids/' . $basename);
        if (file_exists($storagePath)) {
            return asset('storage/valid-ids/' . $basename);
        }

        if (str_starts_with($this->valid_id, 'uploads/')) {
            $uploadPath = public_path($this->valid_id);
            if (file_exists($uploadPath)) {
                return asset($this->valid_id);
            }
        }

        $storagePath = storage_path('app/public/' . $this->valid_id);
        if (file_exists($storagePath)) {
            return asset('storage/' . $this->valid_id);
        }

        return asset('uploads/valid-ids/' . $basename);
    }

    public function getApprovedPickupCountAttribute()
    {
        return $this->equipmentRequests()
            ->where('status', 'approved')
            ->count();
    }

    public function getPendingEquipmentRequestsCountAttribute()
    {
        return $this->equipmentRequests()
            ->where('status', 'pending')
            ->count();
    }

    public function getPickedUpEquipmentCountAttribute()
    {
        return $this->equipmentRequests()
            ->where('status', 'picked_up')
            ->count();
    }

    public function getActiveEquipmentRequestsCountAttribute()
    {
        return $this->equipmentRequests()
            ->whereIn('status', ['pending', 'approved', 'picked_up'])
            ->count();
    }
}