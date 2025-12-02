<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/MarketplaceVariant.php
class MarketplaceVariant extends Model
{
    protected $fillable = [
        'marketplace_product_id',
        'name',
        'duration_days',
        'price',
        'is_active',
    ];

    public function product()
    {
        return $this->belongsTo(MarketplaceProduct::class, 'marketplace_product_id');
    }
}
