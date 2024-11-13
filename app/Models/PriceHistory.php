<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    use HasFactory;

    protected $table = 'price_histories';

    protected $fillable = [
        'product_id',
        'store_id',
        'price',
        'previous_price',
    ];

    /**
     * Get the product that owns the PriceHistory
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function getFormattedPriceAttribute()
    {
        // Guaranies, PY
        return number_format($this->price, 0, ',', '.');
    }

    public function getFormattedPreviousPriceAttribute()
    {
        return $this->previous_price ? number_format($this->previous_price, 0, ',', '.') : null;
    }
}
