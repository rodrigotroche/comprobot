<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingListItem extends Model
{
    use HasFactory;

    protected $fillable = ['store_id', 'shopping_list_id', 'product_id', 'quantity'];

    // Relación con la lista de compras
    public function shoppingList()
    {
        return $this->belongsTo(ShoppingList::class);
    }

    // Relación con el producto
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
