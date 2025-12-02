<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/MarketplaceProduct.php
class MarketplaceProduct extends Model
{
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
}
