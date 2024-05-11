@extends('layouts.home_master')
@section('content')
    <section class="login">
        <div class="row align-items-center text-center">
            <div class="col-md-6">
                <div class="site-logo">
                    <img src="{{ asset('front/images/SK.webp') }}" alt="">
                </div>
                <div class="section-heading">
                    <h2>Welcome back!</h2>
                    <h4>Welcome to Social King</h4>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-block">
                    @if ($message = Session::get('success'))
                        <div class="alert alert_msg">
                            <p class="text-success">{{ $message }}</p>
                        </div>
                    @endif
                    @error('error')
                        <div class="alert alert_msg">
                            <p class="text-danger">{{ $message }}</p>
                        </div>
                    @enderror
                    <form action="{{ route('login.perform') }}" method="post" id="loginForm">
                        @csrf
                        <div class="form-group">
                            <input class="form-control" id="email" type="email" placeholder="Email" name="email">
                        </div>
                        <div class="form-group">
                            <input class="form-control" id="password" type="password" placeholder="Password"
                                name="password">
                        </div>
                        <div class="form-group mb-3">
                            <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                            @if ($errors->has('g-recaptcha-response'))
                                <div class="text-danger">{{ $errors->first('g-recaptcha-response') }}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <div class="error"></div>
                        </div>
                        <div class="form-group">
                            {{-- <input type="button" value="login" class="form-control btn button" onclick="checkUser()"> --}}
                            <input type="submit" value="submit" class="form-control btn button">
                        </div>
                        <p>Do not have account?</p>
                        <div class="form-group">
                            <a href="{{ route('register.show') }}"><button type="button"
                                    class="form-control btn button">Register</button></a>
                        </div>
                        <div>
                            <p><a href="{{ route('forgot_pass') }}" class="link">Forgot Password ?</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- OTP Modal -->
    <div class="modal fade otp_modal" id="otp" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="otpLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="otpLabel">OTP VERIFICATION</h1>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <span>Welcome, </span><strong id="userName"></strong><br>
                        <span>Please enter OTP to verify.</span>
                    </div>
                    <div class="form-group otpDiv">

                    </div>
                    <div id="otpInput" class="inputs d-flex flex-row justify-content-center mt-2">
                        <input class="m-2 text-center form-control rounded" type="text" id="first" maxlength="1" />
                        <input class="m-2 text-center form-control rounded" type="text" id="second" maxlength="1" />
                        <input class="m-2 text-center form-control rounded" type="text" id="third" maxlength="1" />
                        <input class="m-2 text-center form-control rounded" type="text" id="fourth" maxlength="1" />
                        <input class="m-2 text-center form-control rounded" type="text" id="fifth" maxlength="1" />
                        <input class="m-2 text-center form-control rounded" type="text" id="sixth" maxlength="1" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="button" onclick="checkOTP()">Submit</button>
                </div>
            </div>
        </div>
    </div>
@endsection
