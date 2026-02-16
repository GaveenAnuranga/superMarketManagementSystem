<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ViewStoreController extends Controller
{
    //GET all the data of all the items in inventory
    public function __invoke()
    {
        $products = Product::all();
        return view('store', compact('products'));
    }
}
