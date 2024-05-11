<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Models\AccountActivity;
use App\Models\PaymentHistory;
use App\Models\Setting;
use App\Rules\ReCaptcha;
use Illuminate\Support\Str;
use Mail;
use Validator;
use DB;
use Hash;
use Nette\Utils\Json;

class RegisterController extends Controller
{
    /**
     * Display register page.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        if (auth()->guard('web')->user()) {
            return redirect()->route('home');
        }

        $level1 = "";
        if (isset($request->ref) && $request->ref) {
            $level1 = $request->ref;
        }
        return view('auth.register', compact('level1'));
    }

    /**
     * Handle account registration request
     *
     * @param RegisterRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            // 'username' => 'required|string|max:10|regex:/^[a-zA-Z0-9]+$/',
            'username' => 'required|string|max:10|alpha_num',
            'email' => 'required|unique:users,email|max:25',
            'phone' => 'required',
            'password' => 'required|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            // 'confirm_password' => 'required|same:password',
            'g-recaptcha-response' => 'required',
        ], [
            'password.required' => 'Please enter password',
            'password.min' => 'Please enter password must be at least 6 characters',
            'password.regex' => 'Your password must be more than 6 characters long, should contain at-least 1 Uppercase, 1 Lowercase, 1 Numeric and 1 special character.',
            'confirm_password.required' => 'Please enter confirm password',
            'confirm_password.same' => 'confirm password does not match',
            'g-recaptcha-response.required' => 'Google recaptcha is required.'
        ]);
        $input = $request->all();

        if ($request->password) {
            $input['password'] = Hash::make($request->password);
        }
        $token = Str::random(64);
        $input['remember_token'] = $token;
        $input['ref_number'] = refNumberGenerate();

        if (isset($request->level_1) && $request->level_1) {
            $level1User = User::where('ref_number', $request->level_1)->first();
            $input['level_2'] = $level1User->level_1;
        }

        $setting = Setting::first();
        $credit = 0.00;
        if ($setting->sign_up == 1) {
            $credit =  $setting->sign_up_amount;
        }
        $input['amount'] = $credit;
        $user = User::create($input);
        if ($setting->sign_up == 1) {
            $paymentHistory = PaymentHistory::create([
                'user_id' => $user->id,
                'paid_by' => "Sing Up Bonus",
                'status' => "Success",
                'amount' => 0,
                'amount_without_gst' => 0,
                'bonus' => $setting->sign_up_amount,
            ]);
        }

        $ipAddress = $request->ip();
        $currentUserInfo = \Location::get($ipAddress);
        if ($currentUserInfo) {
            $country = $currentUserInfo->countryName . '(' . $currentUserInfo->countryCode . ')';
            $city = $currentUserInfo->cityName;

            AccountActivity::create([
                'action' => 'Register',
                'ip_address' => $ipAddress,
                'country' => $country,
                'city' => $city,
                'user_id' => $user->id,
            ]);
        }

        $message = '';
        $userEmail = $request->email;
        if ($user) {
            // return view('mail.user_varify',compact('token'));
            Mail::send('mail.user_varify', ['token' => $token], function ($message) use ($userEmail) {
                // $message->from(env("MAIL_FROM_ADDRESS"));
                $message->to($userEmail);
                $message->subject('Registration Link');
            });
        }

        return redirect()->route('login')
            ->with('success', 'Your account has been registered successfully, Please verify your email before login');
    }

    public function userVarify($token)
    {
        $user = User::where('remember_token', $token)->first();

        $message = 'Sorry your email cannot be identified.';

        if (!is_null($user)) {

            if (!$user->email_verified_at) {
                $user->update([
                    'remember_token' => null,
                    'email_verified_at' => now(),
                    'status' => 'Activate'
                ]);
                $message = "Your e-mail is verified successfully. You can now login.";
            } else {
                $message = "Your e-mail is already verified. You can now login.";
            }
        }

        return redirect()->route('login')->with('success',$message);
    }
    function scriptCall($id)
    {
        $setting = Setting::first();
        $usd_to_inr = 82.0;
        if ($setting && isset($setting->toArray()['usd_to_inr'])) {
            $usd_to_inr = $setting->toArray()['usd_to_inr'];
        }
        set_time_limit(-1);
        if ($id == 'whEGOrPreViAstOWNdAH') {
            $_temp = [];
            $userAmount = DB::select('SELECT * FROM `user_credits`');
            if (count($userAmount)) {
                foreach ($userAmount as $_key => $_value) {
                    if ($_value->currency == '$') {
                        $_temp[$_value->email] = number_format($_value->credits, 2);
                    } else {
                        $_temp[$_value->email] = number_format(($_value->credits / $usd_to_inr), 2);
                    }
                }
            }

            $user =  DB::select('SELECT * FROM customer WHERE `is_use` = 0 AND `id` > 28440');
            if (count($user)) {
                foreach ($user as $key => $value) {
                    $amount = (isset($_temp[$value->email]) ? $_temp[$value->email] : 0);
                    $email_verified_at = date('Y-m-d H:i:s', strtotime('now'));
                    $user_type = 0;
                    $status = 'Activate';
                    $is_new = 0;
                    $password = Hash::make($value->password);
                    $remember_token = Str::random(64);
                    $ref_number = refNumberGenerate();
                    try {
                        if (!User::where('email', '=', $value->email)->exists()) {
                            User::create([
                                'username' => $value->username,
                                'email' => $value->email,
                                'email_verified_at' => $email_verified_at,
                                'password' => $password,
                                'remember_token' => $remember_token,
                                'user_type' => $user_type,
                                'ref_number' => $ref_number,
                                'amount' => $amount,
                                'status' => $status,
                                'is_new' => $is_new,
                                'phone' => $value->contact_no,
                            ]);
                            DB::select('UPDATE `customer` SET `is_use`=1 WHERE `id`=' . $value->id);
                            print_r('Name : ' . $value->username . ' Email : ' . $value->email . "<br>");
                        }
                    } catch (Exception $e) {
                        print_r($e->getMessage());
                        print_r("<br>");
                    }
                }
            }
        } else {
            echo "You do not have permission to do that action.";
        }
    }

    public function AmountUpdate()
    {
        $setting = Setting::first();
        $usd_to_inr = 82.0;
        if ($setting && isset($setting->toArray()['usd_to_inr'])) {
            $usd_to_inr = $setting->toArray()['usd_to_inr'];
        }
        set_time_limit(-1);

        $_temp = [];
        $userAmount = DB::select('SELECT * FROM `user_credits`');
        if (count($userAmount)) {
            foreach ($userAmount as $_key => $_value) {
                $_temp[$_value->email] = number_format(($_value->credits / $usd_to_inr), 2);
            }
        }

        $user =  DB::select('SELECT * FROM customer');
        if (count($user)) {
            foreach ($user as $key => $value) {
                $amount = (isset($_temp[$value->email]) ? $_temp[$value->email] : 0);
                try {
                    if (User::where('email', '=', $value->email)->exists()) {
                        User::where('email', '=', $value->email)
                            ->update([
                                'amount' => $amount
                            ]);
                        DB::select('UPDATE `customer` SET `is_use`=1 WHERE `id`=' . $value->id);
                        print_r('Name : ' . $value->username . ' Email : ' . $value->email . "<br>");
                    }
                } catch (Exception $e) {
                    print_r($e->getMessage());
                    print_r("<br>");
                }
            }
        }
    }
}
