<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientHealthScore extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'client_id', 'engagement_score', 'churn_risk_score',
        'churn_risk_level', 'factors', 'ai_notes', 'calculated_at',
    ];

    protected $casts = [
        'factors'        => 'array',
        'calculated_at'  => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function getRiskColorAttribute(): string
    {
        return match($this->churn_risk_level) {
            'critical' => 'error',
            'high'     => 'warning',
            'medium'   => 'blue-light',
            default    => 'success',
        };
    }

    public function getEngagementColorAttribute(): string
    {
        if ($this->engagement_score >= 75) return 'success';
        if ($this->engagement_score >= 50) return 'blue-light';
        if ($this->engagement_score >= 25) return 'warning';
        return 'error';
    }
}
