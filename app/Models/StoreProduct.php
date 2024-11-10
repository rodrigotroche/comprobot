<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreProduct extends Model
{
    use HasFactory;

    protected $table = 'store_products';

    protected $fillable = [
        'store_id',
        'product_id',
        'reference_id',
        'sku',
        'url',
        'image',
        'previous_price',
        'price',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function hasDiscount()
    {
        return $this->previous_price && $this->price < $this->previous_price;
    }

    /* return the formatted price in PYG */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', '.');
    }

    public function getFormattedPreviousPriceAttribute()
    {
        return $this->previous_price ? number_format($this->previous_price, 0, ',', '.') : null;
    }
}
