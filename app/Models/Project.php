<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Project extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'client_id', 'retainer_id', 'owner_id', 'name', 'slug',
        'description', 'status', 'priority', 'budget', 'budget_spent', 'progress',
        'start_date', 'due_date', 'completed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'budget' => 'decimal:2',
        'budget_spent' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function retainer(): BelongsTo
    {
        return $this->belongsTo(Retainer::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function deliverables(): HasMany
    {
        return $this->hasMany(Deliverable::class);
    }

    public function aiSummaries(): MorphMany
    {
        return $this->morphMany(AiSummary::class, 'summarizable');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'active'    => 'success',
            'on_hold'   => 'warning',
            'cancelled' => 'error',
            'completed' => 'gray',
            default     => 'blue-light',
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

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
