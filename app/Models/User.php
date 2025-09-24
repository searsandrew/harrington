<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
