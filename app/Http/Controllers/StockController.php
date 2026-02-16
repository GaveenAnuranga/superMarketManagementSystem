<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class StockController extends Controller
{
    //PUT new stock of added items
    public function __invoke(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0',
        ]);

        $product = Product::findOrFail($request->product_id);
        $product->increment('stock', $request->quantity);
        $products = Product::all();
        
        return response()->json([
            'success' => true,
            'message' => 'Stock updated!',
            'product' => $product->fresh(),
            'products' => $products
        ]);
    }
}
