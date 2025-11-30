<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'code',
        'user_id',
        'product_id',
        'product_variant_id',
        'buyer_sku_code',
        'target',
        'email',
        'method',
        'base_price',
        'subtotal',
        'admin_fee',
        'total',
        'profit',
        'payment_method',
        'payment_status',
        'status',
        'digiflazz_ref',
        'digiflazz_status',
        'request_payload',
        'response_payload',
        'paid_at',
        'completed_at',
        'failed_at',
    ];

    protected $casts = [
        'base_price' => 'integer',
        'subtotal' => 'integer',
        'admin_fee' => 'integer',
        'total' => 'integer',
        'profit' => 'integer',
        'request_payload' => 'array',
        'response_payload' => 'array',
        'paid_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];
    // relasi ke OrderPayment
    public function payments()
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(OrderPayment::class)->latestOfMany();
    }

    // helper kecil buat label di invoice nanti
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'wallet' => 'Saldo Maitri',
            'paydisini' => 'Paydisini',
            default => ucfirst($this->payment_method),
        };
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public static function generateCode(): string
    {
        $prefix = 'MP-';

        // cari kode terakhir yang pakai prefix MP-
        $last = static::where('code', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('code');

        // ambil angka di belakang prefix, lalu +1
        $number = $last
            ? ((int) substr($last, strlen($prefix))) + 1
            : 1;

        // hasil: MP-00001, MP-00002, dst
        return sprintf('%s%05d', $prefix, $number);
    }
}
