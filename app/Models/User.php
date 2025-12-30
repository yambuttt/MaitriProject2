<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    protected $casts = [
        // yang lama biarkan
        'maitri_balance' => 'integer',
        'payment_pin_set_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Wallet / PIN helpers
    |--------------------------------------------------------------------------
    */

    public function hasPaymentPin(): bool
    {
        return !is_null($this->payment_pin_hash);
    }

    public function checkPaymentPin(string $pin): bool
    {
        if (!$this->hasPaymentPin()) {
            return false;
        }

        return Hash::check($pin, $this->payment_pin_hash);
    }

    public function setPaymentPin(string $pin): void
    {
        $this->payment_pin_hash = Hash::make($pin);
        $this->payment_pin_set_at = now();
        $this->save();
    }

    public function incrementBalance(int $amount, ?string $description = null): void
    {
        // Nanti kita sambungkan ke WalletTransaction model
        $before = $this->maitri_balance;
        $after = $before + $amount;

        $this->maitri_balance = $after;
        $this->save();

        \App\Models\WalletTransaction::create([
            'user_id' => $this->id,
            'type' => 'credit',
            'amount' => $amount,
            'balance_before' => $before,
            'balance_after' => $after,
            'description' => $description ?? 'Topup saldo Maitri',
        ]);
    }

    public function decrementBalance(int $amount, ?string $description = null): void
    {
        $before = $this->maitri_balance;
        $after = $before - $amount;

        $this->maitri_balance = $after;
        $this->save();

        \App\Models\WalletTransaction::create([
            'user_id' => $this->id,
            'type' => 'debit',
            'amount' => $amount,
            'balance_before' => $before,
            'balance_after' => $after,
            'description' => $description ?? 'Penggunaan saldo Maitri',
        ]);
    }
    public function canPayWithSaldo(int $amount): bool
    {
        return $this->hasPaymentPin() && $this->maitri_balance >= $amount;
    }
    public function affiliateLevel()
    {
        return $this->belongsTo(\App\Models\AffiliateLevel::class, 'affiliate_level_id');
    }
    public function pointRedemptions()
    {
        return $this->hasMany(\App\Models\PointRedemption::class);
    }


}
