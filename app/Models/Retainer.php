<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Retainer extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'client_id', 'name', 'description', 'monthly_value',
        'hours_included', 'start_date', 'end_date', 'status', 'billing_cycle', 'services',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'monthly_value' => 'decimal:2',
        'services' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'active'    => 'success',
            'paused'    => 'warning',
            'cancelled' => 'error',
            'completed' => 'gray',
            default     => 'blue-light',
        };
    }
}
