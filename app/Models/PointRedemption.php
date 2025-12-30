<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointRedemption extends Model
{
    protected $fillable = [
        'user_id',
        'method',
        'points',
        'amount',
        'status',
        'phone',
        'proof_path',
        'admin_note',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'points' => 'integer',
        'amount' => 'integer',
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getMethodLabelAttribute(): string
    {
        return $this->method === 'wallet' ? 'Saldo Maitri' : 'Uang Cash';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'instant'  => 'Instant',
            'pending'  => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default    => $this->status,
        };
    }
}
