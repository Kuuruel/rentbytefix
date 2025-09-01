<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Rental;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        
        // Verify signature
        if ($hashed !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }
        
        $orderId = $request->order_id;
        $transactionStatus = $request->transaction_status;
        $fraudStatus = $request->fraud_status ?? null;
        
        Log::info('Midtrans webhook received', [
            'order_id' => $orderId,
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus
        ]);
        
        // Find rental by bill_id
        $rental = Rental::where('bill_id', $orderId)->first();
        
        if (!$rental) {
            Log::error('Rental not found for order_id: ' . $orderId);
            return response()->json(['message' => 'Rental not found'], 404);
        }
        
        $property = Property::find($rental->property_id);
        
        if (!$property) {
            Log::error('Property not found for rental: ' . $rental->id);
            return response()->json(['message' => 'Property not found'], 404);
        }
        
        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                // Payment successful
                $rental->update([
                    'payment_status' => 'paid',
                    'payment_date' => now()
                ]);
                
                $property->update(['status' => 'Rented']);
                
                Log::info('Payment successful, property status updated to Rented', [
                    'rental_id' => $rental->id,
                    'property_id' => $property->id
                ]);
                break;
                
            case 'pending':
                // Payment pending, keep Processing status
                $rental->update(['payment_status' => 'pending']);
                break;
                
            case 'deny':
            case 'cancel':
            case 'expire':
            case 'failure':
                // Payment failed, revert to Available
                $rental->update([
                    'payment_status' => 'failed',
                    'status' => 'cancelled'
                ]);
                
                $property->update(['status' => 'Available']);
                
                Log::info('Payment failed, property status reverted to Available', [
                    'rental_id' => $rental->id,
                    'property_id' => $property->id,
                    'transaction_status' => $transactionStatus
                ]);
                break;
        }
        
        return response()->json(['message' => 'OK']);
    }
}
