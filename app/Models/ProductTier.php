<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'tier_level',
        'name',
        'unit_multiplier',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'tier_level' => 'integer',
            'unit_multiplier' => 'integer',
            'price' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

