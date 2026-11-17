<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'published_date',
        'is_active',
    ];

    protected $casts = [
        'published_date' => 'date',
        'is_active' => 'boolean',
    ];

    // ------------------------------------------------------------
    // Scopes
    // ------------------------------------------------------------

    // Active announcements only
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Nakikita sa public: active at hindi pa naka-schedule sa hinaharap */
    public function scopePublished($query)
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('published_date')
                                 ->orWhere('published_date', '<=', now()->toDateString()));
    }

    /** Pinakabago muna (published_date, kung wala ay created_at) */
    public function scopeNewestFirst($query)
    {
        return $query->orderByRaw('COALESCE(published_date, DATE(created_at)) DESC')
                     ->orderByDesc('id');
    }

    // ------------------------------------------------------------
    // Accessors (para sa public pages)
    // ------------------------------------------------------------

    public function getDisplayDateAttribute()
    {
        return $this->published_date ?? $this->created_at;
    }

    public function getExcerptAttribute(): string
    {
        $html = preg_replace('/<\/(p|div|li|h[1-6])>|<br\s*\/?>/i', ' ', (string) $this->content);
        $text = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($html))));

        return Str::limit($text, 170);
    }

    public function getReadMinutesAttribute(): int
    {
        $words = preg_match_all('/\S+/u', strip_tags((string) $this->content));

        return max(1, (int) ceil($words / 200));
    }

    public function getIsNewAttribute(): bool
    {
        return $this->display_date->greaterThanOrEqualTo(now()->subDays(7)->startOfDay());
    }

    public function getUrlAttribute(): string
    {
        $slug = Str::slug($this->title);

        return route('public.announcements.show', $slug ? [$this->id, $slug] : [$this->id]);
    }

    public function isPublished(): bool
    {
        return $this->is_active
            && (is_null($this->published_date) || $this->published_date->lte(today()));
    }

    // ------------------------------------------------------------
    // Relationships (existing)
    // ------------------------------------------------------------

    /**
     * Users who have marked this announcement as read.
     */
    public function readByUsers()
    {
        return $this->belongsToMany(User::class, 'announcement_user')
                    ->withPivot('read_at')
                    ->withTimestamps();
    }

    /**
     * Check if a specific user has read this announcement.
     */
    public function isReadByUser($userId)
    {
        return $this->readByUsers()->where('user_id', $userId)->exists();
    }
}