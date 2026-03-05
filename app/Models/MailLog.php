<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MailLogStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'to_email',
        'to_name',
        'original_to_email',
        'subject',
        'body',
        'mailable_class',
        'is_debug_redirect',
        'status',
        'error_message',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at'           => 'datetime',
            'is_debug_redirect' => 'boolean',
            'status'            => MailLogStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isSent(): bool
    {
        return $this->status === MailLogStatus::Sent;
    }

    public function isFailed(): bool
    {
        return $this->status === MailLogStatus::Failed;
    }

    public function isPending(): bool
    {
        return $this->status === MailLogStatus::Pending;
    }
}
