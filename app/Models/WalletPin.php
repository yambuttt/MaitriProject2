<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletPin extends Model
{
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected $fillable = ['user_id','pin_hash','last_changed_at','tries','locked_until'];

    protected $casts = [
        'last_changed_at' => 'datetime',
        'locked_until'    => 'datetime',
        'tries'           => 'integer',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
