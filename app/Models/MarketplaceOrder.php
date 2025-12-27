<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/MarketplaceOrder.php
class MarketplaceOrder extends Model
{
    protected $fillable = [
        'invoice_number',
        'user_id',
        'marketplace_product_id',
        'marketplace_variant_id',
        'customer_email',
        'customer_phone',
        'price',
        'fee',
        'total_amount',
        'payment_method',
        'payment_status',
        'status',
        'user_note',
        'admin_note',
        'processed_by_admin_id',
        'paid_at',
        'rejected_at',
        'finished_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'rejected_at' => 'datetime',
        'finished_at' => 'datetime',
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(MarketplaceOrderPayment::class);
    }
    public function product()
    {
        return $this->belongsTo(MarketplaceProduct::class, 'marketplace_product_id')->withTrashed();
    }

    public function variant()
    {
        return $this->belongsTo(MarketplaceVariant::class, 'marketplace_variant_id')->withTrashed();
    }
}
