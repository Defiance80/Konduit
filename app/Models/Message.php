<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = ['thread_id', 'user_id', 'body', 'is_internal', 'read_at'];

    protected $casts = ['read_at' => 'datetime', 'is_internal' => 'boolean'];

    public function thread(): BelongsTo { return $this->belongsTo(MessageThread::class); }
    public function user(): BelongsTo   { return $this->belongsTo(User::class); }

    public function isRead(): bool      { return $this->read_at !== null; }
}
