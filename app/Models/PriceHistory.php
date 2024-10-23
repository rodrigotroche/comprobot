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
    ];

    public function getFormattedPriceAttribute()
    {
        // Guaranies, PY
        return number_format($this->price, 0, ',', '.');
    }
}
