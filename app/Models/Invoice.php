<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'client_id', 'project_id', 'retainer_id',
        'invoice_number', 'status', 'subtotal', 'tax_rate', 'tax_amount', 'total',
        'notes', 'issued_date', 'due_date', 'paid_at',
    ];

    protected $casts = [
        'issued_date' => 'date',
        'due_date'    => 'date',
        'paid_at'     => 'datetime',
        'subtotal'    => 'decimal:2',
        'tax_rate'    => 'decimal:2',
        'tax_amount'  => 'decimal:2',
        'total'       => 'decimal:2',
    ];

    public function client(): BelongsTo  { return $this->belongsTo(Client::class); }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function retainer(): BelongsTo{ return $this->belongsTo(Retainer::class); }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    public function isOverdue(): bool
    {
        return !in_array($this->status, ['paid','void']) && $this->due_date->isPast();
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'paid'    => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
            'sent'    => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
            'viewed'  => 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400',
            'overdue' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
            'void'    => 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400',
            default   => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
        };
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Invoice $invoice) {
            $last = static::withoutGlobalScopes()->latest('id')->first();
            $next = $last ? (int) substr($last->invoice_number, 4) + 1 : 1000;
            $invoice->invoice_number = 'INV-' . str_pad($next, 5, '0', STR_PAD_LEFT);
        });
    }
}
