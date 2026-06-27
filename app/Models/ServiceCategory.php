<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'name', 'color', 'sort_order'];

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'category_id')->orderBy('sort_order');
    }
}
