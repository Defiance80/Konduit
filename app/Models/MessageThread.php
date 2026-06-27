<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MessageThread extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'client_id', 'subject', 'type', 'participant_ids', 'last_message_at'];

    protected $casts = [
        'participant_ids'  => 'array',
        'last_message_at'  => 'datetime',
    ];

    public function client(): BelongsTo  { return $this->belongsTo(Client::class); }
    public function messages(): HasMany  { return $this->hasMany(Message::class, 'thread_id')->latest(); }
    public function latestMessage()      { return $this->messages()->latest()->first(); }
}
