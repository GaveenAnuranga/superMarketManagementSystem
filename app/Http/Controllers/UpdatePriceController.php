<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class UpdatePriceController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'price' => 'required|numeric|min:0.01'
        ]);

        $product = Product::find($request->product_id);
        $product->price = $request->price;
        $product->save();

        $products = Product::all();

        return response()->json([
            'success' => true,
            'message' => 'Price updated successfully!',
            'products' => $products
        ]);
    }
}
