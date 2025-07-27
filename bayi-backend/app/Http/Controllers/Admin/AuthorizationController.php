<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libraries\Response\Responder;
use App\Models\Admins;
use Auth;
use Illuminate\Http\Request;
use Response;

/**
 * Authorization Controller for web
 */
class AuthorizationController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.home');
        }
        return view('admin.pages.auth-login');
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.home');
        }
        $credentials = $request->only('email', 'password');
        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.home');
        } else {
            //with error notMatch
            return redirect()->route('admin.auth.login')->withErrors(['notMatch' => 'Email veya şifre hatalı.'])->withInput();
        }
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.auth.login');
    }
}
