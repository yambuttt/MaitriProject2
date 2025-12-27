<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// app/Models/MarketplaceProduct.php
class MarketplaceProduct extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'marketplace_category_id',
        'name',
        'slug',
        'description',
        'thumbnail',
        'is_active',
    ];

    public function category()
    {
        return $this->belongsTo(MarketplaceCategory::class, 'marketplace_category_id');
    }

    public function variants()
    {
        return $this->hasMany(MarketplaceVariant::class);
    }

    public function images()
    {
        return $this->hasMany(MarketplaceProductImage::class, 'marketplace_product_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function orders()
    {
        return $this->hasMany(MarketplaceOrder::class, 'marketplace_product_id');
    }


}
