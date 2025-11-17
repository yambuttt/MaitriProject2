<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'digiflazz_variant_id',
        'buyer_sku_code',
        'name',
        // 'base_price' DIHILANGKAN dari fillable
        'markup_rp',
        'is_active',
    ];

    protected $casts = [
        'markup_rp' => 'integer',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function digiflazzVariant()
    {
        return $this->belongsTo(DigiflazzVariant::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * Base price “single source”:
     * SELALU ambil dari digiflazz_variants kalau relasi ada.
     * Kalau tidak ada relasi → dianggap 0.
     */
    public function getBasePriceAttribute($value): int
    {
        // kalau sudah di-eager load
        if ($this->relationLoaded('digiflazzVariant') && $this->digiflazzVariant) {
            return (int) $this->digiflazzVariant->base_price;
        }

        // kalau belum di-load, tetap coba ambil
        if ($this->digiflazz_variant_id) {
            $master = $this->digiflazzVariant; // lazy load
            if ($master) {
                return (int) $master->base_price;
            }
        }

        // fallback: kalau benar-benar tidak ada master (varian manual)
        return 0;
    }

    /**
     * Harga jual akhir:
     * base_price (dari Digiflazz) + markup varian / produk.
     */
    public function getFinalPriceAttribute(): int
    {
        $base = $this->base_price; // lewat accessor di atas

        $markup = (int) ($this->markup_rp ?? $this->product->markup_rp ?? 0);

        return $base + $markup;
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
