<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        $product = Product::with(['storeProducts.store', 'priceHistories'])->find($product->id);
        // return $product;
        return view('frontend.products.show', compact('product'));
    }
}
