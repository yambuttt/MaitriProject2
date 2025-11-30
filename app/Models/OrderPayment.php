<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'method',
        'provider',
        'amount',
        'status',
        'paydisini_unique_code',
        'paydisini_pay_id',
        'paydisini_service_id',
        'request_payload',
        'response_payload',
        'callback_payload',
        'expired_at',
        'paid_at',
    ];

    protected $casts = [
        'amount'           => 'integer',
        'request_payload'  => 'array',
        'response_payload' => 'array',
        'callback_payload' => 'array',
        'expired_at'       => 'datetime',
        'paid_at'          => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // label manis buat di UI nanti
    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'wallet'                   => 'Saldo Maitri',
            'paydisini_qris'           => 'Paydisini QRIS',
            'paydisini_va_mandiri'     => 'Paydisini VA Mandiri',
            'paydisini_alfamart'       => 'Paydisini Alfamart',
            'paydisini_indomaret'      => 'Paydisini Indomaret',
            default                    => ucfirst(str_replace('_', ' ', $this->method)),
        };
    }
}
