<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    // Columns that exist as real DB columns (not serialized into `data` JSON)
    public static function getCustomColumns(): array
    {
        return ['id', 'name', 'slug', 'email', 'phone', 'website', 'logo', 'timezone', 'plan', 'status', 'trial_ends_at'];
    }

    protected $fillable = [
        'id', 'name', 'slug', 'email', 'phone', 'website', 'logo',
        'timezone', 'plan', 'status', 'trial_ends_at', 'data',
    ];

    protected $casts = [
        'data' => 'array',
        'trial_ends_at' => 'datetime',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'tenant_id');
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'tenant_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'tenant_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'tenant_id');
    }

    public function retainers(): HasMany
    {
        return $this->hasMany(Retainer::class, 'tenant_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
