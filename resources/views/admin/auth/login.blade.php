@extends('layouts.admin_login')
@section('content')
    <div class="login-box">
        <div class="login-logo">
            <img width="" src="{{ asset('image/socialking-logo.jpg') }}">
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                @if (\Session::has('error'))
                    <div>
                        <p class="login-box-msg" style="color: red;">{!! \Session::get('error') !!}</p>
                    </div>
                @endif
                <form method="POST" action="{{ route('login_perform') }}" id="loginForm">
                    @csrf
                    <div class="input-group mb-3">
                        <input id="email" type="email" class="form-control @error('username') is-invalid @enderror"
                            name="username" value="{{ old('username') }}" placeholder="Email" required autocomplete="email"
                            autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        @error('username')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="input-group mb-3">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" placeholder="Password" required autocomplete="current-password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    {{-- <div class="form-group mb-3">
                        <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                        @if ($errors->has('g-recaptcha-response'))
                            <div class="text-danger">{{ $errors->first('g-recaptcha-response') }}</div>
                        @endif
                    </div> --}}
                    <div class="form-group">
                        <div class="error"></div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                        </div>
                        <div class="col-4">
                            {{-- <button type="button" class="btn btn-primary btn-block"
                                onclick="checkUser()">{{ __('Login') }}</button> --}}
                            <button type="submit">Login</button>
                        </div>
                    </div>
                </form>
                <p class="mb-1">
                    @if (Route::has('password.request'))
                        <a class="btn btn-link" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    @endif
                </p>
            </div>
        </div>
    </div>


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
