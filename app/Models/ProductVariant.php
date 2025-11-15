<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'buyer_sku_code',
        'digiflazz_variant_id',
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

    public function digiflazzVariant()
    {
        return $this->belongsTo(DigiflazzVariant::class);
    }

    public function getFinalPriceAttribute(): int
    {
        $base = $this->base_price ?? 0;
        $markup = $this->markup_rp ?? ($this->product->markup_rp ?? 0);

        return $base + $markup;
    }

    public function usesDigiflazz(): bool
    {
        return !is_null($this->digiflazz_variant_id);
    }

}
