<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/MarketplaceCategory.php
class MarketplaceCategory extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'is_active',
    ];

    public function products()
    {
        return $this->hasMany(MarketplaceProduct::class);
    }
}
