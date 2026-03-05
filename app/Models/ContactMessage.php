<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ContactSubject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMessage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'is_read',
        'is_starred',
        'is_archived',
        'reply_body',
        'replied_by',
        'replied_at',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'is_read'     => 'boolean',
            'is_starred'  => 'boolean',
            'is_archived' => 'boolean',
            'replied_at'  => 'datetime',
            'subject'     => ContactSubject::class,
        ];
    }

    public function repliedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    public function isReplied(): bool
    {
        return $this->replied_at !== null;
    }

    public function subjectLabel(): string
    {
        return $this->subject?->label() ?? $this->attributes['subject'] ?? '-';
    }

    public function subjectColor(): string
    {
        return $this->subject?->color() ?? 'blue';
    }
}
