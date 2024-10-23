<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingList extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id', 'store_id'];

    // Relación con el usuario que posee la lista
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(ShoppingListItem::class);
    }

    // Relación con los productos en la lista
    public function products()
    {
        return $this->belongsToMany(Product::class, 'shopping_list_items')
            ->withPivot(['quantity', 'id'])  // Incluimos la cantidad
            ->withTimestamps();
    }
}
