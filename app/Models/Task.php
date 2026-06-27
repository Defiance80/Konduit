<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'project_id', 'section_id', 'client_id', 'assignee_id',
        'created_by', 'parent_task_id', 'title', 'description', 'status',
        'priority', 'due_date', 'completed_at', 'estimated_hours', 'tags', 'sort_order',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'completed_at' => 'datetime',
        'tags'         => 'array',
        'estimated_hours' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(TaskSection::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_task_id')->orderBy('sort_order');
    }

    public function isOverdue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && !$this->completed_at;
    }

    public function isCompleted(): bool
    {
        return $this->status === 'done' || $this->completed_at !== null;
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'urgent' => 'text-error-500',
            'high'   => 'text-warning-500',
            'medium' => 'text-blue-500',
            'low'    => 'text-gray-400',
            default  => 'text-gray-300',
        };
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'in_progress' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
            'review'      => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
            'done'        => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
            default       => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
        };
    }

    public function scopeIncomplete($query)
    {
        return $query->whereNull('completed_at')->where('status', '!=', 'done');
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_task_id');
    }
}
