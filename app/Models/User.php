<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Gender;
use App\Enums\RoleSlug;
use App\Enums\UserType;
use App\Mail\ResetPasswordMail;
use App\Mail\VerifyEmailMail;
use App\Traits\HasPermission;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasPermission, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'type',
        'role_id',
        'email_verified_at',
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
        'notify_comment_approved',
        'notify_work_status',
        'notify_new_comment',
        'notify_admin_mails',
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
            'type'                 => UserType::class,
            'gender'               => Gender::class,
            'is_public'               => 'boolean',
            'show_email'              => 'boolean',
            'show_last_seen'          => 'boolean',
            'allow_messages'          => 'boolean',
            'notify_comment_approved' => 'boolean',
            'notify_work_status'      => 'boolean',
            'notify_new_comment'      => 'boolean',
            'notify_admin_mails'      => 'boolean',
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

    public function editorImages(): HasMany
    {
        return $this->hasMany(EditorImage::class);
    }

    public function goldenPenPeriods(): HasMany
    {
        return $this->hasMany(GoldenPenPeriod::class);
    }

    public function goldenBrushPeriods(): HasMany
    {
        return $this->hasMany(GoldenBrushPeriod::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function writerApplications(): HasMany
    {
        return $this->hasMany(WriterApplication::class);
    }

    public function activeGoldenPenPeriod(): HasOne
    {
        $today = now()->toDateString();

        return $this->hasOne(GoldenPenPeriod::class)
            ->where('starts_at', '<=', $today)
            ->where('ends_at', '>=', $today)
            ->latest('ends_at');
    }

    public function activeGoldenBrushPeriod(): HasOne
    {
        $today = now()->toDateString();

        return $this->hasOne(GoldenBrushPeriod::class)
            ->where('starts_at', '<=', $today)
            ->where('ends_at', '>=', $today)
            ->latest('ends_at');
    }

    public function hasRole(RoleSlug $role): bool
    {
        return $this->role?->slug === $role->value;
    }

    public function isAdmin(): bool
    {
        return $this->type === UserType::Admin;
    }

    public function isSuperAdmin(): bool
    {
        return $this->type === UserType::SuperAdmin;
    }

    public function isYazar(): bool
    {
        return $this->hasRole(RoleSlug::Yazar);
    }

    public function hasActiveGoldenPen(): bool
    {
        if ($this->relationLoaded('activeGoldenPenPeriod')) {
            return $this->activeGoldenPenPeriod !== null;
        }

        if ($this->relationLoaded('goldenPenPeriods')) {
            $today = now()->toDateString();

            return $this->goldenPenPeriods->contains(function (GoldenPenPeriod $period) use ($today): bool {
                return $period->starts_at?->toDateString() <= $today
                    && $period->ends_at?->toDateString() >= $today;
            });
        }

        return $this->goldenPenPeriods()
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->exists();
    }

    public function hasActiveGoldenBrush(): bool
    {
        if ($this->relationLoaded('activeGoldenBrushPeriod')) {
            return $this->activeGoldenBrushPeriod !== null;
        }

        if ($this->relationLoaded('goldenBrushPeriods')) {
            $today = now()->toDateString();

            return $this->goldenBrushPeriods->contains(function (GoldenBrushPeriod $period) use ($today): bool {
                return $period->starts_at?->toDateString() <= $today
                    && $period->ends_at?->toDateString() >= $today;
            });
        }

        return $this->goldenBrushPeriods()
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->exists();
    }

    /**
     * Check if user wants to receive a specific mail notification type.
     *
     * Supported types: comment_approved, work_status, new_comment
     */
    public function wantsMailNotification(string $type): bool
    {
        return match ($type) {
            'comment_approved' => $this->notify_comment_approved,
            'work_status'      => $this->notify_work_status,
            'new_comment'      => $this->notify_new_comment,
            'admin_mails'      => $this->notify_admin_mails,
            default            => true,
        };
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? upload_url($this->avatar, 'thumb') : null;
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image ? upload_url($this->cover_image, 'lg') : null;
    }

    public function getProfileUrlAttribute(): string
    {
        return $this->username
            ? route('profile.show', $this->username)
            : route('profile.edit');
    }

    public function sendEmailVerificationNotification(): void
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id'   => $this->getKey(),
                'hash' => sha1($this->getEmailForVerification()),
            ]
        );

        Mail::to($this->email, $this->name)
            ->send(new VerifyEmailMail($this, $verificationUrl));
    }

    public function sendPasswordResetNotification($token): void
    {
        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $this->getEmailForPasswordReset(),
        ], false));

        Mail::to($this->email, $this->name)
            ->send(new ResetPasswordMail($this, $resetUrl));
    }
}
