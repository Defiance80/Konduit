<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'name', 'slug', 'email', 'phone', 'website',
        'industry', 'logo', 'notes', 'status', 'health_score',
    ];

    protected $casts = [
        'health_score' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function retainers(): HasMany
    {
        return $this->hasMany(Retainer::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function deliverables(): HasMany
    {
        return $this->hasMany(Deliverable::class);
    }

    public function activeRetainer(): ?Retainer
    {
        return $this->retainers()->where('status', 'active')->latest()->first();
    }

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=f2f4f7&color=344054&bold=true';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
