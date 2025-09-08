<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


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
            $imgPath = $this->handleImageUpload($request->file('img'));
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
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
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id)
            ],
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $user->role,
        ];

        if ($request->hasFile('img')) {
            if ($user->img) {
                $this->deleteImage($user->img);
            }
            $updateData['img'] = $this->handleImageUpload($request->file('img'));
        }

        $user->update($updateData);

        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    public function updatePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if (Auth::check() && Auth::id() == $user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account');
        }

        if ($user->img) {
            $this->deleteImage($user->img);
        }

        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully');
    }

    public function addUser()
    {
        return view('users.add-user');
    }

    public function usersGrid()
    {
        $users = User::latest()->get();
        return view('users.users-grid', compact('users'));
    }

    public function usersList()
    {
        $users = User::latest()->get();
        return view('users.users-list', compact('users'));
    }

    public function viewProfileAdmin($id = null)
    {
        if ($id === null) {
            $user = Auth::user();
            if (!$user) {
                $user = User::first();
                if (!$user) {
                    abort(404, 'No users found');
                }
            }
        } else {
            $user = User::findOrFail($id);
        }
        
        return view('users.viewProfileAdmin', compact('user'));
    }

    private function handleImageUpload($file)
    {
        try {
            $filename = time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();

            $destination = public_path('assets/images/super-admin');

            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            if ($file->move($destination, $filename)) {
                return $filename;
            }
            
            throw new \Exception('Failed to move uploaded file');
            
        } catch (\Exception $e) {
            Log::error('Image upload failed: ' . $e->getMessage());
            throw new \Exception('Failed to upload image. Please try again.');
        }
    }

    private function deleteImage($filename)
    {
        try {
            $destination = public_path('assets/images/super-admin');
            $filePath = $destination . '/' . $filename;
            
            if (file_exists($filePath)) {
                @unlink($filePath);
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Image deletion failed: ' . $e->getMessage());
            return false;
        }
    }
}