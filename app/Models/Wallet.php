<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'user_id','currency','available_balance','pending_balance','is_frozen','frozen_reason'
    ];

    protected $casts = [
        'available_balance' => 'integer',
        'pending_balance'   => 'integer',
        'is_frozen'         => 'boolean',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function transactions() {
        return $this->hasMany(WalletTransaction::class);
    }

    public function getTotalAttribute(): int {
        return (int)$this->available_balance + (int)$this->pending_balance;
    }
}
