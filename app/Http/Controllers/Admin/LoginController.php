<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Mail;

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::guard('admin')->user()) {
            return redirect()->route('admin.category.index');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Please enter username or e-mail',
            'password.required' => 'Please enter password',
        ]);

        $userEmail = $request->username;
        $password = $request->password;
        if (Auth::guard('admin')->attempt(['email' => $userEmail, 'password' => $password, 'user_type' => 1])) {
            return redirect()->route('dashboard');
        }
        return redirect()->back()->withInput()->with('error', 'Invalid Credentials!!');
    }

    public function logout()
    {
        // Session::flush();

        Auth::guard('admin')->logout();

        // return redirect('admin/login');
        return redirect()->route('admin.login');
    }
}
