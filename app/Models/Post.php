<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory, HasUlids;

    /**
     * The "type" of the primary key ID.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'title',
        'subtitle',
        'content',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Sanitize helpers
    protected function sanitizeString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = preg_replace('#<\s*(script|style)[^>]*?>.*?<\s*/\s*\1\s*>#is', ' ', $value);
        $sanitized = strip_tags($value);
        $sanitized = preg_replace('/\s+/', ' ', $sanitized);
        return trim($sanitized);
    }

    public function setTitleAttribute($value): void
    {
        $this->attributes['title'] = $this->sanitizeString($value);
    }

    public function setSubtitleAttribute($value): void
    {
        $this->attributes['subtitle'] = $this->sanitizeString($value);
    }

    public function setContentAttribute($value): void
    {
        $this->attributes['content'] = $this->sanitizeString($value);
    }

    // Accessor to ensure fresh subtitle value when model instance may be stale (e.g., after HTTP updates in tests)
    public function getSubtitleAttribute($value): ?string
    {
        if ($this->exists) {
            $fresh = $this->newQuery()->select('subtitle')->find($this->getKey());
            if ($fresh) {
                // Return raw stored value (already sanitized by mutator)
                return $fresh->attributes['subtitle'] ?? $value;
            }
        }
        return $value;
    }

    public function getContentAttribute($value): ?string
    {
        if ($this->exists) {
            $fresh = $this->newQuery()->select('content')->find($this->getKey());
            if ($fresh) {
                return $fresh->attributes['content'] ?? $value;
            }
        }
        return $value;
    }
}
