<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceProductImage extends Model
{
    protected $fillable = [
        'marketplace_product_id',
        'path',
        'sort_order',
    ];

    public function product()
    {
        return $this->belongsTo(MarketplaceProduct::class, 'marketplace_product_id');
    }
}
