<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Models\AccountActivity;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Hash;
use Illuminate\Support\Str;
use DB;
use Mail;
use PhpParser\Node\Stmt\TryCatch;
use Stevebauman\Location\Facades\Location;

class LoginController extends Controller
{
    /**
     * Display login page.
     *
     * @return Renderable
     */
    public function show()
    {

        if (auth()->guard('web')->user()) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    public function checkUserLogin(Request $request)
    {
        $userEmail = $request->email;
        $password = $request->password;
        try {
            if (Auth::guard('web')->attempt(['email' => $userEmail, 'password' => $password, 'user_type' => 0])) {
                if (!Auth::guard('web')->user()->email_verified_at) {
                    Auth::guard('web')->logout();
                    echo json_encode([
                        "status" => 3,
                        "message" => 'Email is not verify.',
                    ]);
                    die();
                }
                if (Auth::guard('web')->user()->status == 'Deactivate') {
                    Auth::guard('web')->logout();
                    echo json_encode([
                        "status" => 3,
                        "message" => 'Account is deactivate.',
                    ]);
                    die();
                }
                echo json_encode([
                    "status" => 1,
                    "username" => Auth::user()->username,
                ]);
                $otp = rand(100000, 999999);
                User::where('id', Auth::user()->id)->update(['otp' => $otp]);
                Mail::send('mail.user_otp', ['otp' => $otp], function ($message) use ($userEmail) {
                    $message->to($userEmail);
                    $message->subject('Social king login OTP');
                });
                Auth::guard('web')->logout();
                die();
            } else {
                echo json_encode([
                    "status" => 3,
                    "message" => 'Login details are wrong.',
                ]);
                die();
            }
        } catch (\Throwable $th) {
            dd($th);
        }
    }
    public function checkUserOtp(Request $request)
    {
        $userEmail = $request->email;
        $password = $request->password;
        $otp = $request->otp;
        try {
            if (Auth::guard('web')->attempt(['email' => $userEmail, 'password' => $password, 'user_type' => 0, 'otp' => $otp])) {
                if (!Auth::guard('web')->user()->email_verified_at) {
                    Auth::guard('web')->logout();
                    echo json_encode([
                        "status" => 3,
                        "message" => 'Email is not verify.',
                    ]);
                    die();
                }
                if (Auth::guard('web')->user()->status == 'Deactivate') {
                    Auth::guard('web')->logout();
                    echo json_encode([
                        "status" => 3,
                        "message" => 'Account is deactivate.',
                    ]);
                    die();
                }
                echo json_encode([
                    "status" => 1,
                    "username" => Auth::user()->username,
                ]);
                Auth::guard('web')->logout();
                die();
            } else {
                echo json_encode([
                    "status" => 3,
                    "message" => "OTP wrong",
                ]);
                die();
            }
        } catch (\Throwable $th) {
            dd($th);
        }
    }
    /**
     * Handle account login request
     *
     * @param LoginRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
            'g-recaptcha-response' => 'required',
        ], [
            'email.required' => 'Please enter e-mail',
            'password.required' => 'Please enter password',
            'g-recaptcha-response.required' => 'Google recaptcha is required.'
        ]);

        $userEmail = $request->email;
        $password = $request->password;
        if (Auth::guard('web')->attempt(['email' => $userEmail, 'password' => $password, 'user_type' => 0])) {
            if (!Auth::guard('web')->user()->email_verified_at) {
                Auth::guard('web')->logout();
                return redirect()->route('login')
                    ->with('success', 'You need to confirm your account. We have sent you an activation code, please check your email.');
            }

            if (Auth::guard('web')->user()->status == 'Deactivate') {
                Auth::guard('web')->logout();
                return redirect()->route('login')->withErrors(['error' => 'Your account has been blocked, please contact admin.']);
            }

            $ipAddress = $request->ip();
            $currentUserInfo = \Location::get($ipAddress);
            if ($currentUserInfo) {
                $country = $currentUserInfo->countryName . '(' . $currentUserInfo->countryCode . ')';
                $city = $currentUserInfo->cityName;

                AccountActivity::create([
                    'action' => 'Login',
                    'ip_address' => $ipAddress,
                    'country' => $country,
                    'city' => $city,
                    'user_id' => auth()->guard('web')->user()->id,
                ]);

                $userId = auth()->guard('web')->user()->id;

                $userUpdate = User::find($userId)->update(['ip_address' => $ipAddress]);
            }



            User::where('id', '=', Auth::guard('web')->user()->id)->update(['last_login' => date("Y-m-d h:i:s")]);
            return redirect()->route('user_dashboard');
        }
        return redirect()->back()->withInput()->withErrors(['error' => 'Invalid Credentials!!']);
    }

    public function logout(Request $request)
    {
        // Session::flush();
        $ipAddress = $request->ip();
        $currentUserInfo = \Location::get($ipAddress);
        if ($currentUserInfo) {
            $country = $currentUserInfo->countryName . '(' . $currentUserInfo->countryCode . ')';
            $city = $currentUserInfo->cityName;

            AccountActivity::create([
                'action' => 'Logout',
                'ip_address' => $ipAddress,
                'country' => $country,
                'city' => $city,
                'user_id' => auth()->guard('web')->user()->id,
            ]);
        }

        Auth::guard('web')->logout();

        return redirect()->route('login');
    }

    public function forgotPass()
    {
        return view('auth.forget_password');
    }

    public function passwordRest(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
        ]);

        $token = Str::random(64);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
        ]);
        Mail::send('mail.forgot_password_email', ['token' => $token], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password');
        });

        return redirect()->route('login')->with('success', 'We have e-mailed your password reset link!');
    }

    public function resetPassword($token)
    {
        return view('auth.reset_password', compact('token'));
    }

    public function resetNewPasswrd(Request $request)
    {
        $this->validate($request, [
            'password' => 'required', //|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/
            'confirm_password' => 'required|same:password',
        ]);

        $updatePassword = DB::table('password_resets')
            ->where([
                'token' => $request->token
            ])
            ->first();

        if (!$updatePassword) {
            return back()->withInput()->withErrors('error', 'Invalid token!');
        }

        $user = User::where('email', $updatePassword->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')->where(['email' => $updatePassword->email])->delete();

        return redirect()->route('login')->with('success', 'Your password has been changed!');
    }
}
