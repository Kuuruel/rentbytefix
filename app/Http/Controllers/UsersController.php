<?php

// namespace App\Http\Controllers;

// use App\Models\User;
// use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function codeGenerator()
    {
        return view('aiapplication/codeGenerator');
    }

    public function addUser()
    {
        return view('users/addUser');
    }
    
    public function usersGrid()
    {
        return view('users/usersGrid');
    }
    
    public function usersList()
    {
        return view('users/usersList');
    }
    
    public function viewProfile()
    {
        return view('users/viewProfile');
    }
    
}