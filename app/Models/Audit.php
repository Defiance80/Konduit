<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Audit extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'client_id', 'project_id', 'conducted_by', 'title', 'type',
        'status', 'score', 'findings', 'recommendations', 'ai_analysis',
        'executive_summary', 'visible_to_client', 'audited_at',
    ];

    protected $casts = [
        'findings'         => 'array',
        'recommendations'  => 'array',
        'visible_to_client'=> 'boolean',
        'audited_at'       => 'date',
    ];

    public function client(): BelongsTo      { return $this->belongsTo(Client::class); }
    public function project(): BelongsTo     { return $this->belongsTo(Project::class); }
    public function conductedBy(): BelongsTo { return $this->belongsTo(User::class, 'conducted_by'); }

    public function getScoreColorAttribute(): string
    {
        if ($this->score === null) return 'gray';
        return match (true) {
            $this->score >= 80 => 'success',
            $this->score >= 60 => 'warning',
            default            => 'error',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'seo'         => 'SEO Audit',
            'website'     => 'Website Audit',
            'social'      => 'Social Media Audit',
            'content'     => 'Content Audit',
            'technical'   => 'Technical Audit',
            'performance' => 'Performance Audit',
            default       => 'General Audit',
        };
    }
}
