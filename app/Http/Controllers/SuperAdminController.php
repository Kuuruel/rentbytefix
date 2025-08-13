<?php

namespace App\Http\Controllers;

use App\Models\Tenants;

class SuperAdminController extends Controller
{
    public function index()
    {
        $totalTenants = Tenants::count(); // atau sesuai kebutuhan
        return view('super-admin.index', compact('totalTenants'));
    }

    public function index2()
    {
        $tenants = Tenants::all();
        return view('super-admin.index2', compact('tenants'));
    }

    public function index3()
    {
        $tenants = Tenants::all();
        return view('super-admin.index3', compact('tenants'));
    }

    public function index4()
    {
        return view('super-admin.index4');
    }

    public function index5()
    {
        return view('super-admin.index5');
    }

    public function index6()
    {
        return view('super-admin.index6');
    }

    public function index7()
    {
        return view('super-admin.index7');
    }

    public function index8()
    {
        return view('super-admin.index8');
    }

    public function index9()
    {
        return view('super-admin.index9');
    }
}
