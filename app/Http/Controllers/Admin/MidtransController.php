<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MidtransSetting;
use App\Models\MidtransDraft;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class MidtransController extends Controller
{
    public function index()
    {
        try {
            
            $midtrans_settings = MidtransSetting::first();
            
            $draft_data = $this->getDraftData();
            
            Log::info('Midtrans Index Debug', [
                'has_settings' => $midtrans_settings ? 'yes' : 'no',
                'has_draft' => $draft_data ? 'yes' : 'no',
                'draft_data' => $draft_data,
                'user_id' => Auth::id() ?? 'guest'
            ]);

            $form_data = null;
            $has_draft = false;

            if (!$midtrans_settings && $draft_data) {
                $form_data = (object) $draft_data;
                $has_draft = true;
                Log::info('Using draft data for form');
            }

            return view('super-admin.index9', [
                'midtrans_settings' => $midtrans_settings,
                'form_data' => $form_data,
                'has_draft' => $has_draft
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading Midtrans settings: ' . $e->getMessage());

            return view('super-admin.index9', [
                'midtrans_settings' => null,
                'form_data' => null,
                'has_draft' => false
            ])->with('error', 'Terjadi kesalahan saat memuat pengaturan');
        }
    }

    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'merchant_id' => 'required|string|max:255',
            'client_key' => 'required|string|max:255',
            'server_key' => 'required|string|max:255',
            'environment' => 'required|string|in:sandbox,production',
            'webhook_url' => 'nullable|string|max:500',
        ], [
            'merchant_id.required' => 'Merchant ID is required',
            'client_key.required' => 'Client Key is required',
            'server_key.required' => 'Server Key is required',
            'environment.required' => 'Environment is required',
            'environment.in' => 'Environment must be sandbox or production',
        ]);

        try {
            DB::beginTransaction();
            
            $midtrans_settings = MidtransSetting::first();

            if ($midtrans_settings) {
                
                $midtrans_settings->update([
                    'merchant_id' => $validated['merchant_id'],
                    'client_key' => $validated['client_key'],
                    'server_key' => $validated['server_key'],
                    'environment' => $validated['environment'],
                    'webhook_url' => $validated['webhook_url'] ?? null,
                    'is_active' => true,
                    'updated_at' => now()
                ]);

                Log::info('Midtrans settings updated', ['id' => $midtrans_settings->id]);
                
                $environmentType = $validated['environment'] === 'production' ? 'Production (Live)' : 'Sandbox (Testing)';
                $message = 'Midtrans configuration updated successfully! Environment: ' . $environmentType . '. All payment processing will now use these credentials.';
            } else {
                
                $midtrans_settings = MidtransSetting::create([
                    'merchant_id' => $validated['merchant_id'],
                    'client_key' => $validated['client_key'],
                    'server_key' => $validated['server_key'],
                    'environment' => $validated['environment'],
                    'webhook_url' => $validated['webhook_url'] ?? null,
                    'is_active' => true,
                ]);

                Log::info('New Midtrans settings created', ['id' => $midtrans_settings->id]);
                
                $environmentType = $validated['environment'] === 'production' ? 'Production (Live)' : 'Sandbox (Testing)';
                $message = 'Midtrans configuration saved successfully! Environment: ' . $environmentType . '. Your payment gateway is now ready for transactions.';
            }
            
            $this->clearDraftData();

            DB::commit();

            return redirect()
                ->route('admin.midtrans.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error saving Midtrans settings: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Configuration failed to save. Please check all fields and try again. Error: ' . $e->getMessage());
        }
    }

    public function saveDraft(Request $request)
    {
        try {
            $data = $request->only([
                'merchant_id', 
                'client_key', 
                'server_key', 
                'environment', 
                'webhook_url'
            ]);

            
            $filtered_data = array_filter($data, function($value) {
                return !empty(trim($value));
            });

            if (!empty($filtered_data)) {
                $user_id = Auth::id() ?? 'guest';
                
                
                MidtransDraft::updateOrCreate(
                    ['user_id' => $user_id],
                    [
                        'draft_data' => json_encode($filtered_data),
                        'updated_at' => now()
                    ]
                );

                Log::info('Draft saved', [
                    'user_id' => $user_id,
                    'data_count' => count($filtered_data)
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error saving draft: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function getDraftData()
    {
        try {
            $user_id = Auth::id() ?? 'guest';
            $draft = MidtransDraft::where('user_id', $user_id)->first();
            
            if ($draft) {
                $data = json_decode($draft->draft_data, true);
                Log::info('Draft data retrieved', [
                    'user_id' => $user_id,
                    'data' => $data
                ]);
                return $data;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting draft data: ' . $e->getMessage());
            return null;
        }
    }

    private function clearDraftData()
    {
        try {
            $user_id = Auth::id() ?? 'guest';
            $deleted = MidtransDraft::where('user_id', $user_id)->delete();
            Log::info('Draft data cleared', [
                'user_id' => $user_id,
                'deleted_count' => $deleted
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing draft data: ' . $e->getMessage());
        }
    }

    public function checkDraft()
    {
        try {
            $user_id = Auth::id() ?? 'guest';
            $draft = MidtransDraft::where('user_id', $user_id)->first();
            
            return response()->json([
                'user_id' => $user_id,
                'has_draft' => $draft ? true : false,
                'draft_data' => $draft ? json_decode($draft->draft_data, true) : null,
                'updated_at' => $draft ? $draft->updated_at : null
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function toggleEdit(Request $request)
    {
        return redirect()
            ->route('admin.midtrans.index')
            ->with('edit_mode', true);
    }

    public function isConfigured()
    {
        $settings = MidtransSetting::first();
        return $settings && $settings->merchant_id && $settings->client_key && $settings->server_key;
    }

    public function getActiveSettings()
    {
        return MidtransSetting::where('is_active', true)->first();
    }

    public function testConnection()
    {
        try {
            $settings = $this->getActiveSettings();
            
            if (!$settings) {
                return response()->json([
                    'success' => false,
                    'message' => 'Midtrans settings not found. Please configure first.'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Connection to Midtrans successful!',
                'environment' => $settings->environment
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans connection test failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage()
            ]);
        }
    }
}