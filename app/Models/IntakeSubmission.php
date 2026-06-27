<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntakeSubmission extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'client_id', 'ticket_id', 'name', 'email',
        'company', 'website_url', 'issue_type', 'description', 'priority',
        'ai_classification', 'ai_summary', 'ai_client_message', 'status',
    ];

    protected $casts = [
        'ai_classification' => 'array',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'urgent'  => 'error',
            'high'    => 'warning',
            'medium'  => 'blue-light',
            default   => 'gray',
        };
    }
}
