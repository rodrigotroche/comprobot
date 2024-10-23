<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class MainController extends Controller
{
    public function index()
    {
        // set maximum execution time
        // ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        // return phpinfo();
        return view('frontend.index');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        $products = Product::where('name', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            ->orderBy('created_at', 'desc')
            ->paginate();

        return view('frontend.search', compact('search', 'products'));
    }
}
