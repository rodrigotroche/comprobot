<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreUrl extends Model
{
    use HasFactory;

    protected $table = 'store_urls';

    protected $fillable = [
        'store_id',
        'url',
        'type',
        'enabled',
        'last_synced_at',
        'status',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];
}
