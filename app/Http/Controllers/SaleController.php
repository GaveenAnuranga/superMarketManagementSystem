<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    //POST checkout data and handle checkout process
    public function __invoke(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        
        if ($product->stock >= $request->quantity) {
            $product->decrement('stock', $request->quantity);
            $products = Product::where('stock', '>', 0)->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Sale completed!',
                'products' => $products
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Insufficient stock!'
        ], 400);
    }
}
