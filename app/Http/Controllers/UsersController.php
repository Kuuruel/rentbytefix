<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    public function index()
    {
        $activeCount = User::count();
        return view('super-admin.index', compact('activeCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imgPath = null;
        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $filename = time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();

            $destination = public_path('assets/images/super-admin');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $imgPath = $filename;
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'img' => $imgPath,
        ]);

        return redirect()->route('usersList')->with('success', 'User created successfully');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imgPath = $user->img;
        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $filename = time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();

            $destination = public_path('assets/images/super-admin');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            if ($user->img && file_exists($destination . '/' . $user->img)) {
                @unlink($destination . '/' . $user->img);
            }

            $file->move($destination, $filename);
            $imgPath = $filename;
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'img' => $imgPath,
        ]);

        return back()->with('success', 'Profile updated successfully');
    }

    public function updatePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $user->update([
            'password' => bcrypt($request->password),
        ]);

        return back()->with('success', 'Password updated successfully');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $destination = public_path('assets/images/super-admin');

        if ($user->img && file_exists($destination . '/' . $user->img)) {
            @unlink($destination . '/' . $user->img);
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully');
    }

    public function addUser()
    {
        return view('users.add-user');
    }

    public function usersGrid()
    {
        $users = User::all();
        return view('users.users-grid', compact('users'));
    }

    public function usersList()
    {
        $users = User::all();
        return view('users.users-list', compact('users'));
    }

    public function viewProfile($id = null)
    {
        $user = $id ? User::findOrFail($id) : (auth()->user() ?? User::first());
        return view('users.viewProfile', compact('user'));
    }
}
