<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subcategory extends Model
{
    protected $fillable = ['category_id','name','slug','is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Subcategory $s) {
            $s->slug = Str::slug($s->slug ?: $s->name);
        });

        static::updating(function (Subcategory $s) {
            if ($s->isDirty('slug')) {
                $s->slug = Str::slug($s->slug);
            }
            if ($s->isDirty('name') && blank($s->slug)) {
                $s->slug = Str::slug($s->name);
            }
        });
    }

    public function products(){ return $this->hasMany(\App\Models\Product::class); }

}
