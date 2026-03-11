<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyQuestionAnswer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'daily_question_id',
        'answer_text',
        'user_id',
        'ip_address',
        'cookie_token',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(DailyQuestion::class, 'daily_question_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
