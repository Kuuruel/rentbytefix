<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MidtransSetting;
use Illuminate\Http\Request;

class MidtransController extends Controller
{
    public function index()
    {
        $midtrans_settings = MidtransSetting::first();
        
        return view('admin.midtrans.index9', compact('midtrans_settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'merchant_id' => 'required|string|max:255',
            'client_key' => 'required|string|max:255', 
            'server_key' => 'required|string|max:255',
            'environment' => 'required|in:sandbox,production',
            'webhook_url' => 'nullable|url|max:500',
        ]);

        try {
            // Cek apakah sudah ada setting
            $midtrans_setting = MidtransSetting::first();

            if ($midtrans_setting) {
                // Update existing
                $midtrans_setting->update([
                    'merchant_id' => $request->merchant_id,
                    'client_key' => $request->client_key,
                    'server_key' => $request->server_key,
                    'environment' => $request->environment,
                    'webhook_url' => $request->webhook_url,
                ]);
            } else {
                // Create new
                MidtransSetting::create([
                    'merchant_id' => $request->merchant_id,
                    'client_key' => $request->client_key,
                    'server_key' => $request->server_key,
                    'environment' => $request->environment,
                    'webhook_url' => $request->webhook_url,
                ]);
            }

            return redirect()->back()->with('success', 'Midtrans API Key berhasil disimpan!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
}