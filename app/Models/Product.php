<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'sku',
    ];

    public function storeProducts()
    {
        return $this->hasMany(StoreProduct::class);
    }

    public function priceHistories()
    {
        return $this->hasMany(PriceHistory::class);
    }

    // Relación con las listas de compras
    public function shoppingLists()
    {
        return $this->belongsToMany(ShoppingList::class, 'shopping_list_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
