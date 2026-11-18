<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'owner_id',
        'store_name',
        'warehouse_address',
        'shipping_origin_city',
        'contact_phone',
        'operating_hours',
        'cut_off_time',
        'packing_sla',
        'supported_couriers',
        'packing_standard',
        'return_policy',
        'pic_name',
        'email',
        'receipt_header_title',
        'receipt_logo',
        'receipt_icon',
        'receipt_show_logo',
        'receipt_show_header',
        'receipt_show_address',
        'receipt_show_phone',
        'receipt_show_email',
        'receipt_show_invoice',
        'receipt_show_date',
        'receipt_show_cashier',
        'receipt_show_customer',
        'receipt_show_subtotal',
        'receipt_show_discount',
        'receipt_show_payment_method',
        'receipt_show_change',
        'receipt_footer_notes',
        'receipt_show_footer',
        'receipt_paper_size',
        'pos_pricing_mode',
        'stock_lead_time_days',
        'stock_target_days',
        'printer_bluetooth_config',
    ];

    /**
     * Helper WhatsApp URL untuk chat langsung ke CS Toko
     */
    public function getWhatsappUrlAttribute(): ?string
    {
        $phone = preg_replace('/[^0-9]/', '', (string) $this->contact_phone);
        if (!$phone) {
            return null;
        }

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        }

        $text = rawurlencode("Halo CS {$this->store_name}, saya mitra dropshipper dari OmniPOS ingin koordinasi pesanan / stok.");
        return "https://wa.me/{$phone}?text={$text}";
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stock_lead_time_days' => 'integer',
            'stock_target_days' => 'integer',
            'receipt_show_logo' => 'boolean',
            'receipt_show_header' => 'boolean',
            'receipt_show_address' => 'boolean',
            'receipt_show_phone' => 'boolean',
            'receipt_show_email' => 'boolean',
            'receipt_show_invoice' => 'boolean',
            'receipt_show_date' => 'boolean',
            'receipt_show_cashier' => 'boolean',
            'receipt_show_customer' => 'boolean',
            'receipt_show_subtotal' => 'boolean',
            'receipt_show_discount' => 'boolean',
            'receipt_show_payment_method' => 'boolean',
            'receipt_show_change' => 'boolean',
            'receipt_show_footer' => 'boolean',
            'printer_bluetooth_config' => 'array',
        ];
    }

    /**
     * Pemilik toko supplier.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Tim staff toko (tim media, tim packing, admin).
     */
    public function staff(): HasMany
    {
        return $this->hasMany(User::class, 'supplier_id');
    }

    /**
     * Relasi ke entitas pivot supplier_dropshipper.
     */
    public function supplierDropshippers(): HasMany
    {
        return $this->hasMany(SupplierDropshipper::class, 'supplier_id');
    }

    /**
     * Daftar dropshipper yang terhubung dengan supplier ini.
     */
    public function dropshippers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'supplier_dropshipper', 'supplier_id', 'dropshipper_id')
            ->withPivot(['id', 'wallet_balance', 'status'])
            ->withTimestamps();
    }

    /**
     * Kategori produk yang dimiliki toko.
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'supplier_id');
    }

    /**
     * Inventori produk toko supplier.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'supplier_id');
    }

    /**
     * Seluruh transaksi pesanan toko (POS offline & dropship).
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'supplier_id');
    }
}
