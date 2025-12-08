<?php

// app/Models/DigiflazzVariant.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigiflazzVariant extends Model
{
    protected $fillable = [
        'buyer_sku_code',
        'product_name',
        'brand',
        'category',
        'base_price',
        'status',
        'buyer_product_status',   // <— tambah
        'seller_product_status',  // <— tambah
        'last_synced_at',
        'raw',
    ];

    protected $casts = [
        'base_price' => 'integer',
        'last_synced_at' => 'datetime',
        'raw' => 'array',
        'buyer_product_status' => 'boolean',   // <— tambah
        'seller_product_status' => 'boolean',   // <— tambah
    ];

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
