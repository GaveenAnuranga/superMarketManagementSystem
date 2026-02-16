<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailServiceController extends Controller
{
    public function emailService(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'bill_id' => 'required|integer|exists:bills,id',
        ]);

        $billUrl = url('/bills/' . $request->bill_id);
        
        try {
            Mail::raw(
                "Thank you for shopping with us!\n\nClick the link below to view your bill:\n\n{$billUrl}\n\nThank you, come again!",
                function ($message) use ($request) {
                    $message->to($request->email)
                        ->subject('Your Supermarket Bill');
                }
            );

            return response()->json([
                'success' => true,
                'message' => 'Bill email sent successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }
}
