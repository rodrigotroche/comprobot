<?php

namespace App\Http\Controllers;

use App\Models\ShoppingList;
use Illuminate\Http\Request;
use App\Models\ShoppingListItem;

class ShoppingListItemController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'shopping_list_id' => 'required|exists:shopping_lists,id',
            'product_id' => 'required|exists:products,id',
        ]);

        $shoppingList = ShoppingList::where('id', $request->shopping_list_id)->where('user_id', $request->user()->id)->firstOrFail();

        $shoppingListItem = ShoppingListItem::create([
            'store_id' => $shoppingList->store_id,
            'shopping_list_id' => $request->shopping_list_id,
            'product_id' => $request->product_id,
            'quantity' => 1,
        ]);

        if ($request->wantsJson()) {
            return response()->json($shoppingListItem);
        }

        return redirect()->route('frontend.shopping-lists.show', $shoppingListItem->shopping_list_id);
    }

    public function destroy(Request $request, $id)
    {
        $shoppingListItem = ShoppingListItem::where('id', $id)->whereHas('shoppingList', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->firstOrFail();

        $shoppingListItem->delete();

        if ($request->wantsJson()) {
            return response()->json($shoppingListItem);
        }

        return redirect()->route('frontend.shopping-lists.show', $shoppingListItem->shopping_list_id);
    }
}
