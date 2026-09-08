<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
     * Get the subjects for the user.
     */
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    /**
     * Get the user's favorites.
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get favorited subjects.
     */
    public function favoriteSubjects()
    {
        return $this->morphedByMany(Subject::class, 'favoritable', 'favorites');
    }

    /**
     * Get favorited topics.
     */
    public function favoriteTopics()
    {
        return $this->morphedByMany(Topic::class, 'favoritable', 'favorites');
    }

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
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'payment_valid_until' => 'date',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->is_admin || $this->isProtectedAdmin();
    }

    public function isProtectedAdmin(): bool
    {
        return in_array(
            Str::lower((string) $this->email),
            config('chronos.admin_emails', []),
            true
        );
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }
}
