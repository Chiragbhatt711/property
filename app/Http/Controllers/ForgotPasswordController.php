<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Mail;
use DB;
use Hash;

class ForgotPasswordController extends Controller
{
    public function index()
    {
        return view('auth.forget_password');
    }

    public function paswordReset(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users',
        ],[
            'email.required'=> 'Please enter email',
            'email.email'=> 'Please enter valid email',
            'email.exists'=> 'This email not exists',
        ]);
        $email = $request->email;
        if($email)
        {
            $token = Str::random(64);
            $tokenExists = DB::table('password_resets')
                            ->where([
                                'email' => $email
                            ])
                            ->first();
            if($tokenExists)
            {
                DB::table('password_resets')->where(['email' => $email])->delete();
            }
            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
              ]);
            Mail::send('mail.forgot_password_email',['token' => $token], function($message) use ($email) {
                $message->from(env("MAIL_FROM_ADDRESS"));
                $message->to($email);
                $message->subject('Password Reset');
            });
            return back()->with('message', 'We have e-mailed your password reset link!');
        }
        else {
            return back()->with('message', 'User not found');
        }
    }

    public function showResetPasswordForm($token) {
        if($token)
        {
            $tokenValid = DB::table('password_resets')
                                ->where([
                                    'token' => $token
                                ])
                                ->first();
            if($tokenValid)
            {
                $create_time = Carbon::createFromFormat('Y-m-d H:s:i', $tokenValid->created_at);
                $now = Carbon::now()->format("Y-m-d H:s:i");
                $hours = $create_time->diffInHours($now);
                if($hours < 1)
                {
                    return view('auth.reset_password', ['token' => $token]);
                }
                else
                {
                    DB::table('password_resets')->where(['token'=> $token])->delete();
                    return view('auth.reset_password', ['token' => $token,'error'=>'Token expire']);
                }
            }
            else
            {
                return view('auth.reset_password', ['token' => $token,'error'=>'Token expire']);
            }

        }
        else
        {
            return view('auth.reset_password', ['token' => $token,'error'=>'Page expire']);
        }
    }

    public function submitResetPasswordForm(Request $request)
      {
        $this->validate($request, [
              'password' => 'required|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
              'confirm_password' => 'required|same:password'
          ],[
            'password.required'=> 'Please enter password',
            'password.min'=> 'Please enter password must be at least 6 characters',
            'password.regex'=> 'Your password must be more than 6 characters long, should contain at-least 1 Uppercase, 1 Lowercase, 1 Numeric and 1 special character.',
            'confirm_password.required'=> 'Please enter confirm password',
            'confirm_password.same'=> 'confirm password does not match',
          ]);

          $updatePassword = DB::table('password_resets')
                              ->where([
                                'token' => $request->token
                              ])
                              ->first();
          if(!$updatePassword){
              return back()->withInput()->with('success', 'Invalid token!');
          }
          $email = $updatePassword->email;
          $user = User::where('email', $email)
                      ->update(['password' => Hash::make($request->password)]);

          DB::table('password_resets')->where(['email'=> $email])->delete();

          return redirect('/login')->with('success', 'Your password has been changed!');
      }
}
