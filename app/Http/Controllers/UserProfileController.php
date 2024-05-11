<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Arr;
use Hash;
use Auth;

class UserProfileController extends Controller
{
    public function userProfile()
    {
        $userId = Auth::guard('web')->id();
        $user = User::find($userId);
        return view('my_profile',compact('user'));
    }

    public function profileUpdate(Request $request,$id)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:users',
            'phone' => 'required',
            'username' => 'required',
        ]);

        $input = $request->all();
        $user = User::find($id);

        $user->update($input);

        return redirect()->route('user_profile')
            ->with('success', "Your profile updated successfully.");
    }
}
