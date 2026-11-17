<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        // Auth & identification
        'email',
        'password',
        'first_name',
        'middle_name',
        'last_name',
        'username',           // for resident login
        'role',               // 'resident', 'admin', 'bhw'

        // Personal details
        'dob',                // date of birth
        'age',                // auto-calculated
        'civil_status',
        'gender',
        'occupation',

        // Contact & address
        'phone',              // contact number
        'zone',               // zone/purok
        'address',            // complete address

        // Special flags
        'is_senior',
        'is_pwd',
        'is_indigent',
        'is_voter',

        // Profile photo
        'profile_photo_path',

        // Notification tracking
        'last_viewed_status_at',      // for resident request updates
        'last_viewed_requests_at',    // for admin new requests
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_senior' => 'boolean',
            'is_pwd' => 'boolean',
            'is_indigent' => 'boolean',
            'is_voter' => 'boolean',
            'last_viewed_status_at' => 'datetime',
            'last_viewed_requests_at' => 'datetime',
            'dob' => 'date',
        ];
    }

    /**
     * Automatically calculate age from dob when saving.
     */
    public function setAgeAttribute($value)
    {
        if ($this->dob) {
            $this->attributes['age'] = $this->dob->age;
        }
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Announcements read by this user (pivot table).
     */
    public function readAnnouncements()
    {
        return $this->belongsToMany(Announcement::class, 'announcement_user')
                    ->withPivot('read_at')
                    ->withTimestamps();
    }

    /**
     * Mark all currently active announcements as read by this user.
     */
    public function markAnnouncementsAsRead()
    {
        $announcementIds = Announcement::pluck('id')->toArray();
        $syncData = [];
        foreach ($announcementIds as $id) {
            $syncData[$id] = ['read_at' => now()];
        }
        $this->readAnnouncements()->syncWithoutDetaching($syncData);
    }

    /**
     * Get count of unread announcements.
     */
    public function getUnreadAnnouncementsCount()
    {
        return Announcement::whereNotIn('id', function ($query) {
            $query->select('announcement_id')
                  ->from('announcement_user')
                  ->where('user_id', $this->id);
        })->count();
    }
}