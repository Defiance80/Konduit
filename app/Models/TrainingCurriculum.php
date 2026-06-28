<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingCurriculum extends Model
{
    protected $fillable = ['tenant_id', 'title', 'description', 'color', 'sort_order'];

    public function courses(): HasMany
    {
        return $this->hasMany(TrainingCourse::class, 'curriculum_id')->orderBy('sort_order');
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
