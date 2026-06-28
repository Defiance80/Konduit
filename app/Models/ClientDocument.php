<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientDocument extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'client_id', 'uploaded_by', 'name', 'file_path',
        'document_type', 'notes', 'file_size', 'mime_type',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->document_type) {
            'contract' => 'brand',
            'legal'    => 'error',
            'policy'   => 'warning',
            'proposal' => 'success',
            default    => 'gray',
        };
    }

    public function getFileSizeFormattedAttribute(): string
    {
        if (!$this->file_size) return '';
        $bytes = (int) $this->file_size;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }
}
