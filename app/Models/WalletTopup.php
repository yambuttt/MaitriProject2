<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTopup extends Model
{
    protected $fillable = [
        'user_id',
        'unique_code',
        'pay_id',
        'method',
        'service_id',
        'amount',
        'status',
        'note',
        'request_payload',
        'response_payload',
        'callback_payload',
        'expired_at',
        'paid_at',
    ];

    protected $casts = [
        'amount'            => 'integer',
        'request_payload'   => 'array',
        'response_payload'  => 'array',
        'callback_payload'  => 'array',
        'expired_at'        => 'datetime',
        'paid_at'           => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
