<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

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

        $booking = Transaction::where('order_id', $orderId)->first();
        
        if (!$booking) {
            Log::error('Booking not found for order_id: ' . $orderId);
            return response()->json(['message' => 'Booking not found'], 404);
        }
        
        $property = Property::find($booking->property_id);
        
        if (!$property) {
            Log::error('Property not found for booking: ' . $booking->id);
            return response()->json(['message' => 'Property not found'], 404);
        }
        
        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                $booking->update([
                    'payment_status' => 'paid',
                    'payment_date' => now(),
                    'status' => 'confirmed'
                ]);
                
                $property->update(['status' => 'Rented']);
                
                Log::info('Payment successful, property status updated to Rented', [
                    'booking_id' => $booking->id,
                    'property_id' => $property->id
                ]);
                break;
                
            case 'pending':
                $booking->update(['payment_status' => 'pending']);
                break;
                
            case 'deny':
            case 'cancel':
            case 'expire':
            case 'failure':
                $booking->update([
                    'payment_status' => 'failed',
                    'status' => 'cancelled'
                ]);
                
                $property->update(['status' => 'Available']);
                
                Log::info('Payment failed, property status reverted to Available', [
                    'booking_id' => $booking->id,
                    'property_id' => $property->id,
                    'transaction_status' => $transactionStatus
                ]);
                break;
        }
        
        return response()->json(['message' => 'OK']);
    }
}