<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Tenants;
use App\Models\Renter;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BillController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'renter_id' => 'required|exists:renters,id',
            'property_id' => 'required|exists:properties,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after:today',
        ]);

        try {
            
            $existingBill = Bill::where('tenant_id', $validated['tenant_id'])
                ->where('renter_id', $validated['renter_id'])
                ->where('property_id', $validated['property_id'])
                ->where('amount', $validated['amount'])
                ->where('due_date', $validated['due_date'])
                ->whereDate('created_at', today()) 
                ->first();

            if ($existingBill) {
                return response()->json([
                    'success' => false,
                    'message' => 'Duplicate bill detected. A similar bill already exists for today.',
                    'existing_bill_id' => $existingBill->id
                ], 422);
            }

            
            $duplicateCheck = Bill::where('tenant_id', $validated['tenant_id'])
                ->where('property_id', $validated['property_id'])
                ->where('due_date', $validated['due_date'])
                ->where('status', '!=', 'paid') 
                ->exists();

            if ($duplicateCheck) {
                return response()->json([
                    'success' => false,
                    'message' => 'A bill for this property and due date already exists and is unpaid.',
                ], 422);
            }

            
            $bill = Bill::create([
                'tenant_id' => $validated['tenant_id'],
                'renter_id' => $validated['renter_id'],
                'property_id' => $validated['property_id'],
                'amount' => $validated['amount'],
                'due_date' => $validated['due_date'],
                'order_id' => 'BILL-' . uniqid(),
                'status' => 'pending'
            ]);

            Log::info('Bill created successfully', [
                'bill_id' => $bill->id,
                'tenant_id' => $bill->tenant_id,
                'amount' => $bill->amount,
                'due_date' => $bill->due_date
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bill created successfully',
                'data' => $bill->load(['tenant', 'renter', 'property'])
            ], 201);
        } catch (\Exception $e) {
            Log::error('Bill creation error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $validated
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating bill: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkCreate(Request $request)
    {
        $validated = $request->validate([
            'bills' => 'required|array|min:1',
            'bills.*.tenant_id' => 'required|exists:tenants,id',
            'bills.*.renter_id' => 'required|exists:renters,id',
            'bills.*.property_id' => 'required|exists:properties,id',
            'bills.*.amount' => 'required|numeric|min:0',
            'bills.*.due_date' => 'required|date|after:today',
        ]);

        $createdBills = [];
        $duplicates = [];
        $errors = [];

        foreach ($validated['bills'] as $index => $billData) {
            try {
                
                $existingBill = Bill::where('tenant_id', $billData['tenant_id'])
                    ->where('renter_id', $billData['renter_id'])
                    ->where('property_id', $billData['property_id'])
                    ->where('due_date', $billData['due_date'])
                    ->where('status', '!=', 'paid')
                    ->first();

                if ($existingBill) {
                    $duplicates[] = [
                        'index' => $index,
                        'existing_bill_id' => $existingBill->id,
                        'message' => 'Duplicate bill detected'
                    ];
                    continue;
                }

                
                $bill = Bill::create([
                    'tenant_id' => $billData['tenant_id'],
                    'renter_id' => $billData['renter_id'],
                    'property_id' => $billData['property_id'],
                    'amount' => $billData['amount'],
                    'due_date' => $billData['due_date'],
                    'order_id' => 'BILL-' . uniqid(),
                    'status' => 'pending'
                ]);

                $createdBills[] = $bill;
            } catch (\Exception $e) {
                $errors[] = [
                    'index' => $index,
                    'message' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Bulk bill creation completed',
            'data' => [
                'created' => count($createdBills),
                'duplicates' => count($duplicates),
                'errors' => count($errors),
                'created_bills' => $createdBills,
                'duplicate_details' => $duplicates,
                'error_details' => $errors
            ]
        ]);
    }

    public function checkDuplicate(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'renter_id' => 'required|exists:renters,id',
            'property_id' => 'required|exists:properties,id',
            'amount' => 'required|numeric',
            'due_date' => 'required|date',
        ]);

        $existingBill = Bill::where('tenant_id', $validated['tenant_id'])
            ->where('renter_id', $validated['renter_id'])
            ->where('property_id', $validated['property_id'])
            ->where('due_date', $validated['due_date'])
            ->where('status', '!=', 'paid')
            ->first();

        return response()->json([
            'success' => true,
            'has_duplicate' => $existingBill !== null,
            'existing_bill' => $existingBill ? $existingBill->load(['tenant', 'renter', 'property']) : null
        ]);
    }
}
