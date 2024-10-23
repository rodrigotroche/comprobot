<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Store;
use App\Models\ShoppingList;
use App\Models\Product;

class ShoppingListController extends Controller
{
    public function index()
    {
        $shoppingLists = ShoppingList::orderBy('created_at', 'desc')->paginate();

        return view('frontend.shopping-lists.index', compact('shoppingLists'));
    }

    public function create()
    {
        $stores = Store::all();

        return view('frontend.shopping-lists.create', compact('stores'));
    }

    public function show(Request $request, ShoppingList $shoppingList)
    {
        $shoppingList = ShoppingList::with('products')->findOrFail($shoppingList->id);
        if ($request->wantsJson()) {
            return response()->json($shoppingList);
        }

        return view('frontend.shopping-lists.show', compact('shoppingList'));
    }

    public function edit(Request $request, ShoppingList $shoppingList)
    {
        $stores = Store::all();
        $featuredProducts = Product::inRandomOrder()->limit(5)->get();

        return view('frontend.shopping-lists.edit', compact('shoppingList', 'stores', 'featuredProducts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'store_id' => 'nullable|exists:stores,id',
        ]);

        $user = User::findOrFail($request->user()->id);

        $shoppingList = ShoppingList::create([
            'name' => $request->name,
            'user_id' => $user->id,
            'store_id' => $request->has('store_id') ? $request->store_id : null,
        ]);

        return redirect()->route('frontend.shopping-lists.edit', $shoppingList);
    }
}
