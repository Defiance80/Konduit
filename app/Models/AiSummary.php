<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AiSummary extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'summarizable_type', 'summarizable_id', 'type', 'subject',
        'content', 'client_content', 'visible_to_client', 'generated_by',
        'confidence', 'what_happened', 'why', 'what_next', 'metadata',
    ];

    protected $casts = [
        'confidence'        => 'decimal:2',
        'metadata'          => 'array',
        'visible_to_client' => 'boolean',
    ];

    public function summarizable(): MorphTo
    {
        return $this->morphTo();
    }
}
