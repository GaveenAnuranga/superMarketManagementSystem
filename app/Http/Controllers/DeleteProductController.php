<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class DeleteProductController extends Controller
{  
    //DELETE products
    public function __invoke(Product $product)
    {
        $product->delete();
        $products = Product::all();
        
        return response()->json([
            'success' => true,
            'message' => 'Product removed!',
            'products' => $products
        ]);
    }
}
