<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirect based on role
            $user = Auth::user();
            
            if (!$user->is_active) {
                Auth::logout();
                return back()->with('error', 'Your account has been deactivated.');
            }

            $request->session()->flash('login_welcome', true);
            
            if ($user->isFrontDesk()) {
                return redirect()->route('front-desk.index');
            } elseif ($user->isSectionStaff()) {
                return redirect()->route('section-staff.index');
            } elseif ($user->isSectionOfficer()) {
                return redirect()->route('section.index');
            } elseif ($user->isAdmin()) {
                return redirect()->route('admin.index');
            }

            return redirect()->intended('/dashboard');
        }

        return back()->with('error', 'Invalid username or password.')->withInput();
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
