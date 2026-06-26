<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deliverable extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'project_id', 'client_id', 'name', 'description',
        'status', 'file_path', 'file_url', 'client_feedback',
        'submitted_at', 'approved_at', 'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
