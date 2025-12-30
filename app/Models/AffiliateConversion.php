<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateConversion extends Model
{
    protected $fillable = [
        'affiliate_user_id',
        'buyer_user_id',
        'order_type',
        'order_id',
        'points_awarded',
        'awarded_at',
    ];

    protected $casts = [
        'affiliate_user_id' => 'int',
        'buyer_user_id' => 'int',
        'order_id' => 'int',
        'points_awarded' => 'int',
        'awarded_at' => 'datetime',
    ];

    public function affiliate()
    {
        return $this->belongsTo(User::class, 'affiliate_user_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }
}
