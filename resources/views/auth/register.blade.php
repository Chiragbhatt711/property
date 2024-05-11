@extends('layouts.home_master')

@section('content')
    <section class="login">
        <div class="row text-center">
            <div class="col-md-6">
                <div class="site-logo">
                    <img src="{{ asset('front/images/SK.webp') }}" alt="">
                </div>
                <div class="section-heading">
                    <h2>Join us today!</h2>
                    <h4>Create your Social King account</h4>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-block">
                    {!! Form::open(['route' => 'register.perform', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
                    @csrf
                    {!! Form::hidden('level_1', $level1, ['id' => 'level_1']) !!}
                    <div class="form-group">
                        {!! Form::text('username', null, ['placeholder' => 'User Name', 'class' => 'form-control']) !!}
                        @error('username')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        {!! Form::text('email', isset($_GET['email']) && $_GET['email'] ? $_GET['email'] : null, [
                            'placeholder' => 'Email',
                            'class' => 'form-control',
                        ]) !!}
                        @error('email')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        {!! Form::text('phone', null, ['placeholder' => 'Phone', 'class' => 'form-control']) !!}
                        @error('phone')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        {{-- <input type="password" name="password" class="form-control"> --}}
                        {!! Form::password('password', ['placeholder' => 'Password', 'class' => 'form-control']) !!}
                        @error('password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- <div class="form-group">
                        {!! Form::password('confirm_password', ['placeholder' => 'Confirm Password', 'class' => 'form-control']) !!}
                        @error('confirm_password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div> --}}
                    <div class="form-group mb-3">
                        <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                        @if ($errors->has('g-recaptcha-response'))
                            <div class="text-danger">{{ $errors->first('g-recaptcha-response') }}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Register" class="form-control btn button">
                    </div>
                    <p> Already have an account? </p>
                    <div class="form-group">
                        <a href="{{ route('login') }}" class="form-control btn button">Login</a>
                    </div>
                    <p?>By signing up, you agree to socialking's <a href="{{ route('termsconditions') }}"
                            target="_blank">Terms of Service </a> & <a href="{{ route('privacy_policy') }}"
                            target="_blank">Privacy Policy</a></p>
                        {!! Form::close() !!}
                </div>
            </div>
        </div>
    </section>
@endsection
