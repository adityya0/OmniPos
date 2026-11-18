<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'order_code',
        'shipping_receipt_number',
        'courier_name',
        'shipping_label_file',
        'status',
        'marketplace_deadline',
        'supplier_id',
        'user_id',
        'transaction_type',
        'customer_name',
        'total_cost',
        'subtotal',
        'discount',
        'final_amount',
        'net_profit',
        'payment_method',
        'payment_status',
        'cash_amount',
        'change_amount',
        'notes',
        'cancel_reason',
        'cancelled_by',
        'cancel_stage',
    ];

    protected function casts(): array
    {
        return [
            'total_cost' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'final_amount' => 'decimal:2',
            'net_profit' => 'decimal:2',
            'cash_amount' => 'decimal:2',
            'change_amount' => 'decimal:2',
            'marketplace_deadline' => 'datetime',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dropshipper(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /**
     * Generate unique Invoice: INV-YYYYMMDD-XXXX
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('Ymd');
        $countToday = self::whereDate('created_at', today())->count() + 1;
        $sequence = str_pad((string)$countToday, 4, '0', STR_PAD_LEFT);

        $invoice = "{$prefix}-{$sequence}";
        while (self::where('invoice_number', $invoice)->exists()) {
            $invoice = "{$prefix}-" . str_pad((string)(++$countToday), 4, '0', STR_PAD_LEFT);
        }

        return $invoice;
    }
}
