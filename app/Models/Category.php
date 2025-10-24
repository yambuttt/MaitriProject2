<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Auto-generate slug bila kosong, dan normalisasi bila diubah
    protected static function booted(): void
    {
        static::creating(function (Category $c) {
            if (blank($c->slug)) {
                $c->slug = Str::slug($c->name);
            } else {
                $c->slug = Str::slug($c->slug);
            }
        });

        static::updating(function (Category $c) {
            if ($c->isDirty('slug')) {
                $c->slug = Str::slug($c->slug);
            }
            if ($c->isDirty('name') && blank($c->slug)) {
                $c->slug = Str::slug($c->name);
            }
        });
    }

    public function subcategories()
    {
        return $this->hasMany(\App\Models\Subcategory::class);
    }
    public function products()
    {
        return $this->hasMany(\App\Models\Product::class);
    }

}
