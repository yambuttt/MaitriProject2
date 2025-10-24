<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id','subcategory_id','name','slug',
        'provider','markup_rp','is_active','description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'markup_rp' => 'integer',
    ];

    public function category(){ return $this->belongsTo(Category::class); }
    public function subcategory(){ return $this->belongsTo(Subcategory::class); }

    public function variants(){ return $this->hasMany(\App\Models\ProductVariant::class); }
    


    protected static function booted(): void
    {
        static::creating(function (Product $p) {
            $p->slug = Str::slug($p->slug ?: $p->name);
        });
        static::updating(function (Product $p) {
            if ($p->isDirty('slug')) $p->slug = Str::slug($p->slug);
            if ($p->isDirty('name') && blank($p->slug)) $p->slug = Str::slug($p->name);
        });
    }
}
