<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SaveBillController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'customer_email' => 'required|email',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            // Create the bill
            $bill = Bill::create([
                'customer_email' => $request->customer_email,
            ]);

            // Create bill items
            foreach ($request->items as $item) {
                BillItem::create([
                    'bill_id' => $bill->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Bill saved successfully',
                'bill_id' => $bill->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Bill save error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save bill: ' . $e->getMessage(),
            ], 500);
        }
    }
}
