<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasUlids;

    /**
     * The "type" of the primary key ID.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
        ];
    }

    /**
     * Accessor: determine if the user is an admin based on APP_ADMIN env list.
     */
    public function getIsAdminAttribute(): bool
    {
        // Read from configuration (supports string CSV or JSON array, or array)
        $raw = config('app.admin');

        $ids = [];
        if (is_string($raw)) {
            $trim = trim($raw);
            if ($trim === '') {
                $ids = [];
            } elseif (str_starts_with($trim, '[')) {
                $decoded = json_decode($trim, true);
                if (is_array($decoded)) {
                    $ids = $decoded;
                }
            } else {
                $ids = array_filter(array_map('trim', explode(',', $trim)), fn ($v) => $v !== '');
            }
        } elseif (is_array($raw)) {
            $ids = $raw;
        }

        // Compare as strings (ULIDs)
        $ids = array_map(fn ($v) => (string) $v, $ids);
        $myId = (string) $this->getKey();

        return in_array($myId, $ids, true);
    }

    /**
     * Compute post counts for tabs (Published / Scheduled / Drafts) in a single query.
     * Returns an associative array: ['published' => int, 'scheduled' => int, 'drafts' => int]
     * Uses simple static in-request cache to avoid duplicate queries per request.
     */
    public function postTabCounts(): array
    {
        static $cache = [];
        $key = (string) $this->getKey();
        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $row = Post::query()
            ->where('user_id', $key)
            ->selectRaw(''
                . 'SUM(CASE WHEN is_published = true THEN 1 ELSE 0 END) as published, '
                . 'SUM(CASE WHEN is_published = false AND published_at IS NOT NULL THEN 1 ELSE 0 END) as scheduled, '
                . 'SUM(CASE WHEN is_published = false AND published_at IS NULL THEN 1 ELSE 0 END) as drafts'
            )
            ->first();

        $counts = [
            'published' => (int) ($row->published ?? 0),
            'scheduled' => (int) ($row->scheduled ?? 0),
            'drafts' => (int) ($row->drafts ?? 0),
        ];

        return $cache[$key] = $counts;
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        // Trim, collapse whitespace, split and filter out empty tokens
        $name = (string) $this->name;
        $normalized = trim(preg_replace('/\s+/', ' ', $name) ?? '');
        if ($normalized === '') {
            return '';
        }

        $parts = array_values(array_filter(explode(' ', $normalized), fn ($p) => $p !== ''));

        // Take first characters of first two parts, preserving original case
        $initials = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= mb_substr($part, 0, 1);
        }

        return $initials;
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
