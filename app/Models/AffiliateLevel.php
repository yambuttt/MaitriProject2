<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateLevel extends Model
{
    protected $fillable = [
        'name',
        'window_days',
        'digiflazz_points',
        'marketplace_points',
        'is_active',
    ];

    protected $casts = [
        'window_days' => 'int',
        'digiflazz_points' => 'int',
        'marketplace_points' => 'int',
        'is_active' => 'bool',
    ];
}
