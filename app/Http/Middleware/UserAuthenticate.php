<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Session;

class UserAuthenticate
{

    public function handle($request, Closure $next, $guard = 'user')
    {
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                Session::flash('flash_error', 'You are not logged in.');
                return redirect()->guest('userLogin');
            }
        }

        return $next($request);
    }
}