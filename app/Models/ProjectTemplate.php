<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ProjectTemplate extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'name', 'description', 'default_status',
        'estimated_days', 'task_sections', 'deliverable_names', 'is_shared',
    ];

    protected $casts = [
        'task_sections'     => 'array',
        'deliverable_names' => 'array',
        'is_shared'         => 'boolean',
    ];

    public function getSectionCountAttribute(): int
    {
        return count($this->task_sections ?? []);
    }

    public function getTaskCountAttribute(): int
    {
        return collect($this->task_sections ?? [])->sum(fn ($s) => count($s['tasks'] ?? []));
    }
}
