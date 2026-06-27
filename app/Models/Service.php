<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'category_id', 'name', 'description', 'what_you_get',
        'price', 'price_type', 'features', 'estimated_hours', 'status', 'sort_order',
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'estimated_hours'=> 'decimal:2',
        'features'       => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function getPriceFormattedAttribute(): string
    {
        if (!$this->price) return 'Custom quote';
        return match ($this->price_type) {
            'hourly'  => '$' . number_format($this->price, 0) . '/hr',
            'monthly' => '$' . number_format($this->price, 0) . '/mo',
            'fixed'   => '$' . number_format($this->price, 0),
            default   => 'Custom quote',
        };
    }
}
