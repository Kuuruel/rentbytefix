<?php

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
    public function statisticUsers(Request $request)
    {
        try {
            $search = $request->get('search', '');

            $query = Tenants::with('user')->orderBy('id', 'desc');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('status', 'LIKE', '%' . $search . '%')
                        ->orWhere('country', 'LIKE', '%' . $search . '%')
                        ->orWhere('email', 'LIKE', '%' . $search . '%');
                });
            }

            $tenants = $query->paginate(6);

            $topCountry = Tenants::select('country')
                ->selectRaw('COUNT(*) as tenant_count')
                ->whereNotNull('country')
                ->where('country', '!=', '')
                ->groupBy('country')
                ->orderBy('tenant_count', 'desc')
                ->first();

            $countryName = $topCountry ? $topCountry->country : 'Indonesia';
            $countryTenantCount = $topCountry ? $topCountry->tenant_count : Tenants::count();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'tenants' => $tenants,
                    'count' => $tenants->count(),
                    'countryName' => $countryName,
                    'countryTenantCount' => $countryTenantCount
                ]);
            }

            return view('super-admin.index4', compact('tenants', 'countryName', 'countryTenantCount', 'search'));
        } catch (\Exception $e) {
            Log::error('Tenant statistic error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to search tenants',
                    'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to load tenant statistics');
        }
    }

    public function updateTenantProfile(Request $request, $id = null)
    {
        try {
            if ($id) {
                $tenant = Tenants::findOrFail($id);
            } else {
                if (Auth::guard('tenant')->check()) {
                    $tenant = Tenants::findOrFail(Auth::guard('tenant')->id());
                } else {
                    abort(403, 'Unauthorized');
                }
            }

            $request->validate([
                'name'    => 'required|string|max:255',
                'email'   => 'required|email|max:255|unique:tenants,email,' . $tenant->id,
                'country' => 'nullable|string|max:100',
                'status'  => 'nullable|in:Active,Inactive',
                'note'    => 'nullable|string',
                'avatar'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $filename = time() . '_' . preg_replace('/\s+/', '_', strtolower($file->getClientOriginalName()));
                $dest = public_path('assets/images/tenants');

                if (!file_exists($dest)) {
                    mkdir($dest, 0755, true);
                }

                if ($tenant->avatar) {
                    $oldPath = $dest . DIRECTORY_SEPARATOR . $tenant->avatar;
                    if (file_exists($oldPath) && is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                $file->move($dest, $filename);
                $tenant->avatar = $filename;
            }

            $tenant->name    = $request->input('name');
            $tenant->email   = $request->input('email');
            $tenant->country = $request->input('country', $tenant->country);
            $tenant->status  = $request->input('status', $tenant->status);
            $tenant->note    = $request->input('note', $tenant->note);

            $tenant->save();

            return redirect()->back()->with('success', 'profile');

        } catch (\Throwable $e) {
            Log::error('updateTenantProfile error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui profil tenant.');
        }
    }

    public function updateTenantPassword(Request $request, $id)
    {
        $tenant = Tenants::findOrFail($id);

        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $tenant->password = Hash::make($request->password);
        $tenant->save();

        return redirect()
            ->route('viewProfileTenant', $tenant->id)
            ->with('success', 'Password berhasil diperbarui.');
    }

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
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
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
                'status' => 'required|in:Active,Inactive',
                'country' => 'required|string|max:100'
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

            $authUserId = Auth::id();
            $userId = null;

            if ($authUserId && User::where('id', $authUserId)->exists()) {
                $userId = $authUserId;
            } else {
                $systemUser = $this->ensureDefaultUser();
                $userId = $systemUser->id;
            }

            $data['user_id'] = $userId;
            $data['avatar'] = '/assets/images/user-list/user-list1.png';

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
            Log::error('Stack trace: ' . $e->getTraceAsString());

            if (strpos($e->getMessage(), 'user_id') !== false || strpos($e->getMessage(), 'foreign') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'User reference error. Please contact administrator.',
                    'debug' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            if (strpos($e->getMessage(), 'Duplicate entry') !== false || strpos($e->getMessage(), 'UNIQUE') !== false) {
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
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create tenant',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $tenant = Tenants::with('user')->findOrFail($id);
            $tenant = Tenants::with('user')->findOrFail($id);
            return response()->json([
                'success' => true,
                'tenant' => $tenant
            ]);
        } catch (\Exception $e) {
            Log::error('Error showing tenant: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load tenant details',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function viewProfileTenant($id = null)
    {
        try {
            if ($id) {
                $tenant = Tenants::with('user')->findOrFail($id);
            } else {
                if (Auth::guard('tenant')->check()) {
                    $authTenantId = Auth::guard('tenant')->id();
                    $tenant = Tenants::with('user')->findOrFail($authTenantId);
                } else {
                    if (Auth::check()) {
                        $tenant = Tenants::with('user')->where('user_id', Auth::id())->firstOrFail();
                    } else {
                        abort(403, 'Unauthorized');
                    }
                }
            }

            return view('users.viewProfileTenant', compact('tenant'));
        } catch (\Exception $e) {
            Log::error('Error loading tenant profile: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());

            return redirect()->back()->with('error', 'Gagal memuat profil tenant.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $tenant = Tenants::findOrFail($id);

            $tenant = Tenants::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:tenants,email,' . $id,
                'password' => 'nullable|string|min:6',
                'note' => 'nullable|string|max:1000',
                'status' => 'required|in:Active,Inactive',
                'country' => 'required|string|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

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
            Log::error('Stack trace: ' . $e->getTraceAsString());

            if (strpos($e->getMessage(), 'Duplicate entry') !== false || strpos($e->getMessage(), 'UNIQUE') !== false) {
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
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update tenant',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $tenant = Tenants::findOrFail($id);

            $tenant = Tenants::findOrFail($id);
            
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
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tenant',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

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
}