<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class StoreProductController extends Controller
{
    //POST new item
    public function __invoke(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        $product = Product::create($request->all());
        $products = Product::all();
        
        return response()->json([
            'success' => true,
            'message' => 'Product added!',
            'product' => $product,
            'products' => $products
        ]);
    }
}
