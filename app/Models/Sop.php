<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sop extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'sop_category_id', 'created_by', 'title',
        'description', 'content', 'status', 'version',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(SopCategory::class, 'sop_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'published' => 'success',
            'archived'  => 'gray',
            default     => 'warning',
        };
    }
}
