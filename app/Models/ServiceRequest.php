<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'client_id', 'service_id', 'submitted_by', 'title',
        'message', 'status', 'price_quoted', 'agency_response',
    ];

    protected $casts = ['price_quoted' => 'decimal:2'];

    public function client(): BelongsTo      { return $this->belongsTo(Client::class); }
    public function service(): BelongsTo     { return $this->belongsTo(Service::class); }
    public function submittedBy(): BelongsTo { return $this->belongsTo(User::class, 'submitted_by'); }
}
