<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $authenticated = false;

        if (empty($guards)) {
            $guards = ['web', 'tenant'];
        }
        
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $authenticated = true;
                break;
            }
        }
        
        if (!$authenticated) {
            return redirect()->route('showSigninForm');
        }
        
        return $next($request);
    }
}