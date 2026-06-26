<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Ticket extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'client_id', 'project_id', 'assignee_id', 'submitted_by',
        'ticket_number', 'subject', 'description', 'type', 'status', 'priority',
        'attachments', 'internal_notes', 'ai_classification', 'ai_summary', 'resolved_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'internal_notes' => 'array',
        'resolved_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    public function aiSummaries(): MorphMany
    {
        return $this->morphMany(AiSummary::class, 'summarizable');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'open'        => 'blue-light',
            'in_progress' => 'warning',
            'waiting'     => 'orange',
            'resolved'    => 'success',
            'closed'      => 'gray',
            default       => 'gray',
        };
    }

    public function getPriorityBadgeAttribute(): string
    {
        return match ($this->priority) {
            'urgent' => 'error',
            'high'   => 'warning',
            'medium' => 'blue-light',
            default  => 'gray',
        };
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Ticket $ticket) {
            $ticket->ticket_number = $ticket->generateTicketNumber();
        });
    }

    private function generateTicketNumber(): string
    {
        $prefix = 'TKT';
        $last = static::withoutGlobalScopes()->latest('id')->first();
        $next = $last ? (int) substr($last->ticket_number, 3) + 1 : 1000;

        return $prefix . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}
