<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateApplication extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'note',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
