<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingCourse extends Model
{
    protected $fillable = [
        'tenant_id', 'title', 'description', 'category',
        'difficulty', 'estimated_minutes', 'is_published', 'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function lessons(): HasMany
    {
        return $this->hasMany(TrainingLesson::class, 'course_id')->orderBy('sort_order');
    }

    public function completions(): HasMany
    {
        return $this->hasMany(TrainingCompletion::class, 'course_id');
    }

    public function progressForUser(int $userId): int
    {
        $total = $this->lessons()->count();
        if ($total === 0) {
            return 0;
        }

        $completed = TrainingCompletion::where('course_id', $this->id)
            ->where('user_id', $userId)
            ->count();

        return (int) round(($completed / $total) * 100);
    }

    public function getDifficultyColorAttribute(): string
    {
        return match ($this->difficulty) {
            'beginner'     => 'success',
            'intermediate' => 'warning',
            'advanced'     => 'error',
            default        => 'gray',
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'agency_ops'  => 'Agency Operations',
            'marketing'   => 'Marketing Strategy',
            'client_mgmt' => 'Client Management',
            'platform'    => 'Platform Training',
            'ai_tools'    => 'AI & Tools',
            default       => 'General',
        };
    }

    public function getCategoryIconAttribute(): string
    {
        return match ($this->category) {
            'agency_ops'  => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
            'marketing'   => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
            'client_mgmt' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
            'platform'    => 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z',
            'ai_tools'    => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
            default       => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
        };
    }

    public function scopeForTenant($query, ?string $tenantId)
    {
        return $query->where(function ($q) use ($tenantId) {
            $q->whereNull('tenant_id');
            if ($tenantId) {
                $q->orWhere('tenant_id', $tenantId);
            }
        });
    }
}
