<?php

namespace App\Http\Controllers\BackEnd;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\LoginRequest;
use Auth;
use Session;

class LoginController extends Controller
{
    public function login()
    {
        return view('backend.login');
    }

    public function checkLogin(LoginRequest $request)
    {
        $credential = ['email' => $request->email, 'password' => $request->password];
        if (Auth::guard('admin')->attempt($credential)) {
            Session::set('last_login_lang', Auth::guard('admin')->user()->last_login_lang);
            Session::set('admin', Auth::guard('admin')->user());
            return redirect()->intended('dashboard');
        } else {
            Session::flash('flash_error', 'Your Given Credential Is Wrong.');
            return redirect()->route('login');
        }
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        Session::flash('flash_success', 'You Are Successfully Logout.');
        return redirect()->route('login');
    }
}
