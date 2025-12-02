<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceOrderPayment extends Model
{
    protected $fillable = [
        'marketplace_order_id',
        'method',
        'amount',
        'status',
        'paydisini_unique_code',
        'paydisini_pay_id',
        'paydisini_service_id',
        'response_payload',
        'callback_payload',
        'expired_at',
        'paid_at',
    ];

    protected $casts = [
        'response_payload' => 'array',
        'callback_payload' => 'array',
        'paid_at'          => 'datetime',
        'expired_at'       => 'datetime',
    ];

    public function order()
    {
        // ⬅️ PENTING: sebutkan foreign key-nya
        return $this->belongsTo(MarketplaceOrder::class, 'marketplace_order_id');
    }
}
