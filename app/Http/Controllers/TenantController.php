<?php

namespace App\Http\Controllers;

use App\Models\Tenants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenants::orderBy('id', 'desc')->get();
        return view('super-admin.index', compact('tenants'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:tenants,email',
            'properties' => 'nullable|integer',
            'note'       => 'nullable|string'
        ]);

        $data['password'] = Hash::make(Str::random(12));
        $data['user_id']  = Auth::id() ?: 0;

        $tenant = Tenants::create($data); // konsisten: singular variabel

        return response()->json(['success' => true, 'tenant' => $tenant], 201);
    }

    public function update(Request $request, Tenants $tenant)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:tenants,email,' . $tenant->id,
            'properties' => 'nullable|integer',
            'note'       => 'nullable|string'
        ]);

        $tenant->update($data);

        return response()->json(['success' => true, 'tenant' => $tenant]);
    }

    public function destroy(Tenants $tenant)
    {
        $tenant->delete();
        return response()->json(['success' => true]);
    }
}
