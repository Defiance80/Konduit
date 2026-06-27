<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SopCategory extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'name', 'color'];

    public function sops(): HasMany
    {
        return $this->hasMany(Sop::class, 'sop_category_id');
    }
}
