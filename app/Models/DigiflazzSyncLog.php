<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigiflazzSyncLog extends Model
{
    protected $fillable = [
        'type',
        'status',
        'synced_count',
        'trigger',
        'message',
        'context',
    ];

    protected $casts = [
        'synced_count' => 'integer',
        'context'      => 'array',
    ];
}
