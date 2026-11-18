<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'category_id',
        'sku',
        'name',
        'description',
        'length',
        'width',
        'height',
        'weight',
        'cost_price',
        'selling_price',
        'dropship_price',
        'stock',
        'safety_stock',
        'image',
        'marketing_assets_url',
        'is_active',
    ];

    protected $appends = [
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'length' => 'decimal:2',
            'width' => 'decimal:2',
            'height' => 'decimal:2',
            'weight' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'dropship_price' => 'decimal:2',
            'stock' => 'integer',
            'safety_stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    public function tiers(): HasMany
    {
        return $this->hasMany(ProductTier::class, 'product_id')->orderBy('tier_level');
    }

    /**
     * Hitung sisa stok yang dapat dipesan oleh dropshipper dengan memperhitungkan cadangan minimum toko (safety stock).
     */
    public function getAvailableDropshipStockAttribute(): int
    {
        return max(0, (int) $this->stock - (int) ($this->safety_stock ?? 0));
    }

    /**
     * Hitung kuantitas pesanan online dropship yang sedang aktif menunggu packing / approval.
     */
    public function getPendingDropshipQtyAttribute(): int
    {
        if (isset($this->attributes['pending_dropship_qty'])) {
            return (int) $this->attributes['pending_dropship_qty'];
        }

        return (int) $this->orderItems()
            ->whereHas('order', function ($q) {
                $q->where('transaction_type', 'dropship')
                  ->whereIn('status', ['menunggu_approval', 'menunggu_packing', 'dipacking']);
            })
            ->sum('quantity');
    }

    /**
     * Total fisik barang yang saat ini masih berada di toko / gudang (stok kasir + booking online yang belum diserahkan kurir).
     */
    public function getTotalPhysicalStockAttribute(): int
    {
        return (int) $this->stock + $this->pending_dropship_qty;
    }

    /**
     * Generate format SKU unik: [NAMA]-[KATEGORI]-[ANGKA_URUTAN]
     * Contoh produk ke-1: SQU-ELE-101, ke-2: KEM-PAK-102
     */
    public static function generateSku(string $name, ?string $categoryName = null): string
    {
        // 1. Ambil 3 huruf dari Nama Produk
        $cleanName = preg_replace('/[^A-Za-z]/', '', strtoupper($name));
        $nameCode = substr($cleanName, 0, 3) ?: 'PRD';

        // 2. Ambil 3 huruf dari Kategori
        $catCode = 'GEN';
        if ($categoryName) {
            $cleaned = preg_replace('/[^A-Za-z]/', '', strtoupper($categoryName));
            $catCode = substr($cleaned, 0, 3) ?: 'GEN';
        }

        // 3. Hitung urutan produk ascending (berdasarkan total produk toko/sistem saat ini)
        $totalCount = self::count() + 1;
        $counter = 100 + $totalCount; // mulai dari 101, 102, 103, ... (tidak monoton 0001)

        do {
            $sku = "{$nameCode}-{$catCode}-{$counter}";
            $counter++;
        } while (self::where('sku', $sku)->exists());

        return $sku;
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            if (str_starts_with($this->image, 'http')) {
                return $this->image;
            }
            return asset('storage/' . $this->image);
        }

        return 'https://placehold.co/400x400/e2e8f0/475569?text=' . urlencode($this->name);
    }
}
