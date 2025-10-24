<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'wallet_id','type','amount','status','reason_code',
        'reference_id','reference_type','idempotency_key','metadata','posted_at'
    ];

    protected $casts = [
        'amount'   => 'integer',
        'metadata' => 'array',
        'posted_at'=> 'datetime',
    ];

    public function wallet() {
        return $this->belongsTo(Wallet::class);
    }
}
