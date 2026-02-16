<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SellingController extends Controller
{
    //GET data about item that can be sale
    public function __invoke()
    {
        $products = Product::where('stock', '>', 0)->get();
        return view('selling', compact('products'));
    }
}
