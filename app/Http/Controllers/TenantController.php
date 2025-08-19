<?php
// app/Http/Controllers/TenantController.php

namespace App\Http\Controllers;

use App\Models\Tenants;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantController extends Controller
{
    public function index()
    {
        try {
            if (request()->ajax()) {
                $tenants = Tenants::with('user')->orderBy('id', 'desc')->get();
                return response()->json([
                    'success' => true,
                    'tenants' => $tenants
                ]);
            }

            $tenants = Tenants::with('user')->orderBy('id', 'desc')->get();
            return view('super-admin.index2', compact('tenants'));
        } catch (\Exception $e) {
            Log::error('Tenant index error: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to load tenants',
                    'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to load tenants');
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:tenants,email',
                'password' => 'required|string|min:6',
                'note' => 'nullable|string|max:1000',
                'status' => 'required|in:Active,Inactive'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            $data['password'] = Hash::make($data['password']);

            // Handle user_id assignment with fallback
            $userId = Auth::id();

            if (!$userId) {
                // If no authenticated user, try to find the first user or create a default one
                $firstUser = User::first();
                if ($firstUser) {
                    $userId = $firstUser->id;
                } else {
                    // Create a default system user if no users exist
                    $systemUser = User::create([
                        'name' => 'System Admin',
                        'email' => 'system@' . request()->getHost(),
                        'password' => Hash::make('password'),
                        'email_verified_at' => now()
                    ]);
                    $userId = $systemUser->id;
                }
            } else {
                // Verify the authenticated user still exists
                $userExists = User::where('id', $userId)->exists();
                if (!$userExists) {
                    $firstUser = User::first();
                    $userId = $firstUser ? $firstUser->id : null;

                    if (!$userId) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No valid user found in system. Please contact administrator.'
                        ], 500);
                    }
                }
            }

            $data['user_id'] = $userId;
            $data['avatar'] = asset('assets/images/user-list/user-list1.png');

            // Use database transaction for data integrity
            DB::beginTransaction();

            $tenant = Tenants::create($data);
            $tenant->load('user');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tenant created successfully',
                'tenant' => $tenant
            ], 201);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            Log::error('Database error creating tenant: ' . $e->getMessage());

            // Check for specific constraint violations
            if (strpos($e->getMessage(), 'user_id_foreign') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'User reference error. Please contact administrator.'
                ], 500);
            }

            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email address already exists'
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'Database error occurred',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating tenant: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create tenant',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function show(Tenants $tenant)
    {
        try {
            $tenant->load('user');
            return response()->json([
                'success' => true,
                'tenant' => $tenant
            ]);
        } catch (\Exception $e) {
            Log::error('Error showing tenant: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load tenant details',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function update(Request $request, Tenants $tenant)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:tenants,email,' . $tenant->id,
                'password' => 'nullable|string|min:6',
                'note' => 'nullable|string|max:1000',
                'status' => 'required|in:Active,Inactive'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            // Only update password if provided
            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                $data['password'] = Hash::make($data['password']);
            }

            DB::beginTransaction();

            $tenant->update($data);
            $tenant->load('user');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tenant updated successfully',
                'tenant' => $tenant
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            Log::error('Database error updating tenant: ' . $e->getMessage());

            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email address already exists'
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'Database error occurred',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating tenant: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update tenant',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function destroy(Tenants $tenant)
    {
        try {
            DB::beginTransaction();

            $tenantName = $tenant->name;
            $tenant->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Tenant '{$tenantName}' deleted successfully"
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error deleting tenant: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tenant',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Check and create default user if needed
     */
    private function ensureDefaultUser()
    {
        $userCount = User::count();

        if ($userCount === 0) {
            return User::create([
                'name' => 'System Admin',
                'email' => 'admin@' . (request()->getHost() ?: 'localhost'),
                'password' => Hash::make('password'),
                'email_verified_at' => now()
            ]);
        }

        return User::first();
    }
    public function statisticUsers()
    {
        try {
            $tenants = Tenants::with('user')->orderBy('id', 'desc')->get();
            return view('super-admin.index4', compact('tenants'));
        } catch (\Exception $e) {
            Log::error('Tenant statistic error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load tenant statistics');
        }
    }
}
