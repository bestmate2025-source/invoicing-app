<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Invoice extends Model
{
    protected $fillable = ['client_id', 'invoice_number', 'issue_date', 'due_date', 'tax_rate', 'discount', 'notes', 'status'];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'tax_rate' => 'decimal:2',
        'discount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (!$invoice->invoice_number) {
                $prefix = 'INV-' . now()->format('Ym') . '-';
                $last = static::where('invoice_number', 'like', $prefix . '%')->latest('id')->value('invoice_number');
                $next = $last ? ((int) Str::afterLast($last, '-') + 1) : 1;
                $invoice->invoice_number = $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
            }
        });

        static::saving(function (Invoice $invoice) {
            $invoice->recalculateTotals();
        });
    }

    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function items(): HasMany { return $this->hasMany(InvoiceItem::class); }

    public function recalculateTotals(): void
    {
        $subtotal = $this->relationLoaded('items')
            ? $this->items->sum(fn ($item) => $item->quantity * $item->unit_price)
            : ($this->subtotal ?? 0);
        $discount = min((float) $this->discount, $subtotal);
        $taxable = max(0, $subtotal - $discount);
        $this->subtotal = $subtotal;
        $this->discount = $discount;
        $this->tax_amount = round($taxable * ((float) $this->tax_rate / 100), 2);
        $this->total = round($taxable + $this->tax_amount, 2);
    }

    public function getBalanceDueAttribute(): float { return max(0, (float) $this->total - (float) $this->amount_paid); }
}
