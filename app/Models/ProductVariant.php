<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'buyer_sku_code',
        'name',
        'base_price',
        'markup_rp',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'base_price' => 'integer',
        'markup_rp' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // final_price = base_price + (variant_markup ?? product_markup ?? 0)
    public function getFinalPriceAttribute(): int
    {
        $productMarkup = (int) ($this->product->markup_rp ?? 0);
        $selfMarkup = (int) ($this->markup_rp ?? 0);
        return (int) $this->base_price + ($selfMarkup ?: $productMarkup);
    }

}
