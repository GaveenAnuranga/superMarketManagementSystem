<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ManageController extends Controller
{
    //GET inventory list
    public function __invoke()
    {
        $products = Product::all();
        return view('manage', compact('products'));
    }
}
