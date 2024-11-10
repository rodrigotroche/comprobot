<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempProduct extends Model
{
    use HasFactory;

    protected $table = 'temp_products';

    protected $fillable = [
        'store_id',
        'reference_id',
        'name',
        'url',
        'image',
        'current_price',
        'status',
        'last_scraped_at',
    ];

    /**
     * Get the store that owns the TempProduct
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
