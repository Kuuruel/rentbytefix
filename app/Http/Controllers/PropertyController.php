<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Renter;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    public function index()
    {
        return view('landlord.index3');
    }

    /**
     * Get current tenant ID based on authentication
     */
    private function getCurrentTenantId()
    {
        if (Auth::guard('tenant')->check()) {
            return Auth::guard('tenant')->id();
        }

        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            if ($user->tenant_id) {
                return $user->tenant_id;
            }
            if ($user->role === 'admin') {
                return null;
            }
            if ($user->role === 'tenant') {
                return $user->id;
            }
        }
        
        return null;
    }

    public function data(Request $request): JsonResponse
    {
        try {
            Log::info('Properties data method called', ['request' => $request->all()]);
            
            $tenantId = $this->getCurrentTenantId();

            $query = Property::query();

            if ($tenantId !== null) {
                $query->where('tenant_id', $tenantId);
            }

            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('type', 'like', "%{$searchTerm}%")
                      ->orWhere('address', 'like', "%{$searchTerm}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $perPage = $request->get('per_page', 10);
            $properties = $query->orderBy('created_at', 'desc')->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $properties
            ]);
            
        } catch (\Exception $e) {
            Log::error('Properties data error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load properties: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'price' => 'required|numeric|min:0',
                'rent_type' => ['required', Rule::in(['Monthly', 'Yearly'])],
                'status' => ['required', Rule::in(['Available', 'Rented'])]
            ]);

            $tenantId = $this->getCurrentTenantId();
            
            if ($tenantId === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to determine tenant. Please ensure you are properly logged in.'
                ], 403);
            }

            $validated['tenant_id'] = $tenantId;

            $property = Property::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Property created successfully!',
                'property' => $property
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Property store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create property: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Property $property): JsonResponse
    {
        try {
            $tenantId = $this->getCurrentTenantId();
            
            if ($tenantId !== null && $property->tenant_id !== $tenantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this property.'
                ], 403);
            }
            
            return response()->json([
                'success' => true,
                'data' => $property
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load property: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Property $property): JsonResponse
    {
        try {
            $tenantId = $this->getCurrentTenantId();
            
            if ($tenantId !== null && $property->tenant_id !== $tenantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this property.'
                ], 403);
            }
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'price' => 'required|numeric|min:0',
                'rent_type' => ['required', Rule::in(['Monthly', 'Yearly'])],
                'status' => ['required', Rule::in(['Available', 'Rented'])]
            ]);

            $property->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Property updated successfully!',
                'property' => $property->fresh()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update property: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Property $property): JsonResponse
    {
        try {

            $tenantId = $this->getCurrentTenantId();
            
            if ($tenantId !== null && $property->tenant_id !== $tenantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this property.'
                ], 403);
            }
            
            $propertyName = $property->name;
            $property->delete();

            return response()->json([
                'success' => true,
                'message' => "Property '{$propertyName}' has been deleted successfully!"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete property: ' . $e->getMessage()
            ], 500);
        }
    }

       public function getRenterDetails($propertyId)
{
    try {
        // Cari renter yang aktif untuk property ini
        $rental = \App\Models\Renter::where('property_id', $propertyId)
                                    ->latest()
                                    ->first();
        
        if (!$rental) {
            return response()->json([
                'success' => false, 
                'message' => 'No renter found for this property'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'renter_name' => $rental->name,
                'renter_phone' => $rental->phone,
                'renter_email' => $rental->email,
                'renter_address' => $rental->address,
                'start_date' => $rental->start_date,
                'end_date' => $rental->end_date,
                'property_id' => $rental->property_id,
                'created_at' => $rental->created_at,
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error retrieving renter details: ' . $e->getMessage()
        ], 500);
    }
}
}