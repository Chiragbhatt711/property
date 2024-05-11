<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function index()
    {
        return view('auth.change_password');
    }

    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'password:web',
            'password' => 'required|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'confirm_password' => 'required|same:password'
        ],[
          'password.required'=> 'Please enter password',
          'password.min'=> 'Please enter password must be at least 6 characters',
          'password.regex'=> 'Your password must be more than 6 characters long, should contain at-least 1 Uppercase, 1 Lowercase, 1 Numeric and 1 special character.',
          'confirm_password.required'=> 'Please enter confirm password',
          'confirm_password.same'=> 'confirm password does not match',
        ]);

        if($request->password)
        {
            $userId = auth()->guard('web')->user()->id;
            $user = User::where('id', $userId)
                      ->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->back()->with('success','Password change successfully.');
    }
}
