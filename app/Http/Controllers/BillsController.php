<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Product;
use Illuminate\Http\Request;

class BillsController extends Controller
{
    public function __invoke(Request $request, $billId)
    {
        $bill = Bill::findOrFail($billId);
        
        // Get bill items with product details
        $billItems = BillItem::where('bill_id', $billId)
            ->with('product')
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'total' => $item->product->price * $item->quantity
                ];
            });
        
        $total = $billItems->sum('total');
        
        // Get bill history for this customer
        $billHistory = Bill::where('customer_email', $bill->customer_email)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('bills', compact('bill', 'billItems', 'total', 'billHistory'));
    }
}
