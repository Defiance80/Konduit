<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deliverable extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'project_id', 'client_id', 'reviewer_id',
        'name', 'description', 'status', 'version',
        'file_path', 'file_url', 'file_name', 'file_mime', 'file_size',
        'client_feedback', 'rejection_reason',
        'submitted_at', 'approved_at', 'due_date',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'submitted_at' => 'datetime',
        'approved_at'  => 'datetime',
        'file_size'    => 'integer',
        'version'      => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isInReview(): bool
    {
        return $this->status === 'in_review';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isOverdue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && !in_array($this->status, ['approved', 'delivered']);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'in_review' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
            'approved'  => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
            'rejected'  => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
            'delivered' => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
            default     => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'in_review' => 'Awaiting Approval',
            'approved'  => 'Approved',
            'rejected'  => 'Changes Requested',
            'delivered' => 'Delivered',
            default     => 'In Progress',
        };
    }

    public function hasFile(): bool
    {
        return !empty($this->file_path) || !empty($this->file_url);
    }

    public function getFileSizeFormattedAttribute(): ?string
    {
        if (!$this->file_size) return null;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        $size = $this->file_size;
        while ($size >= 1024 && $i < 3) {
            $size /= 1024;
            $i++;
        }
        return round($size, 1) . ' ' . $units[$i];
    }
}
