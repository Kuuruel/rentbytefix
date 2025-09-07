<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MidtransSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function index()
    {
        try {
            // Ambil data setting yang sudah disimpan (hanya 1 record global)
            $midtrans_settings = MidtransSetting::first();

            // Log untuk debugging
            Log::info('Loading Midtrans Settings', [
                'found' => $midtrans_settings ? 'yes' : 'no',
                'data' => $midtrans_settings ? $midtrans_settings->toArray() : null
            ]);

            // Menggunakan view super-admin.index9
            return view('super-admin.index9', compact('midtrans_settings'));
        } catch (\Exception $e) {
            Log::error('Error loading Midtrans settings: ' . $e->getMessage());

            return view('super-admin.index9', [
                'midtrans_settings' => null
            ])->with('error', 'Terjadi kesalahan saat memuat pengaturan');
        }
    }

    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'merchant_id' => 'required|string|max:255',
            'client_key' => 'required|string|max:255',
            'server_key' => 'required|string|max:255',
            'environment' => 'required|string|in:sandbox,production',
            'webhook_url' => 'nullable|string|max:500',
        ], [
            'merchant_id.required' => 'Merchant ID wajib diisi',
            'client_key.required' => 'Client Key wajib diisi',
            'server_key.required' => 'Server Key wajib diisi',
            'environment.required' => 'Environment wajib dipilih',
            'environment.in' => 'Environment harus sandbox atau production',
        ]);

        try {
            DB::beginTransaction();

            // Cek apakah sudah ada data
            $midtrans_settings = MidtransSetting::first();

            if ($midtrans_settings) {
                // Update data yang sudah ada
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
            } else {
                // Buat data baru
                $midtrans_settings = MidtransSetting::create([
                    'merchant_id' => $validated['merchant_id'],
                    'client_key' => $validated['client_key'],
                    'server_key' => $validated['server_key'],
                    'environment' => $validated['environment'],
                    'webhook_url' => $validated['webhook_url'] ?? null,
                    'is_active' => true,
                ]);

                Log::info('New Midtrans settings created', ['id' => $midtrans_settings->id]);
            }

            DB::commit();

            return redirect()
                ->route('admin.midtrans.index')
                ->with('success', 'Your Midtrans configuration has been successfully saved and will remain stored!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error saving Midtrans settings: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An error has occurred: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk mengecek apakah settings sudah dikonfigurasi
     */
    public function isConfigured()
    {
        $settings = MidtransSetting::first();
        return $settings && $settings->merchant_id && $settings->client_key && $settings->server_key;
    }

    /**
     * Method untuk mendapatkan settings aktif
     */
    public function getActiveSettings()
    {
        return MidtransSetting::where('is_active', true)->first();
    }
}
