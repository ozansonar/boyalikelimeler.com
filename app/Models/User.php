<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RoleSlug;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role_id',
        'bio',
        'about',
        'avatar',
        'cover_image',
        'location',
        'website',
        'birthdate',
        'gender',
        'instagram',
        'twitter',
        'youtube',
        'tiktok',
        'spotify',
        'interests',
        'is_public',
        'show_email',
        'show_last_seen',
        'allow_messages',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'birthdate'         => 'date',
            'interests'         => 'array',
            'is_public'         => 'boolean',
            'show_email'        => 'boolean',
            'show_last_seen'    => 'boolean',
            'allow_messages'    => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function literaryWorks(): HasMany
    {
        return $this->hasMany(LiteraryWork::class);
    }

    public function hasRole(RoleSlug $role): bool
    {
        return $this->role?->slug === $role->value;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(RoleSlug::Admin);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(RoleSlug::SuperAdmin);
    }

    public function isYazar(): bool
    {
        return $this->hasRole(RoleSlug::Yazar);
    }

    public function getRouteKeyName(): string
    {
        return 'username';
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? upload_url($this->avatar) : null;
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image ? upload_url($this->cover_image) : null;
    }

    public function getProfileUrlAttribute(): string
    {
        return $this->username
            ? route('profile.show', $this->username)
            : route('profile.edit');
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification());
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
