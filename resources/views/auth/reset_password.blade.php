@extends('layouts.home_master')
@section('content')
@if ($message = Session::get('success'))
    <div class="alert alert_msg">
        <p>{{ $message }}</p>
    </div>
@endif
@if (isset($error) && $error)
    <div class="alert alert-danger">
        <p>{{ $error }}</p>
    </div>
@endif
<section class="login">
    <div class="row align-items-center text-center">
        <div class="col-md-6">
            <div class="site-logo">
                <img src="{{ asset('front/images/SK.png') }}" alt="">
            </div>
            <div class="section-heading">
                <h2>Welcome back!</h2>
                <h4>Welcome to Social King</h4>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-block">
                {!! Form::open(array('route' => 'reset_password_post','method'=>'POST','enctype'=>'multipart/form-data')) !!}
                @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="form-group">
                        <input class="form-control" id="password" type="password" placeholder="Password"
                                name="password">
                        @error('password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input class="form-control" id="confirm_password" type="password" placeholder="Conform Password*"
                                name="confirm_password">
                        @error('confirm_password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @error('error')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Reset Password" class="form-control btn button">
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>
@endsection
