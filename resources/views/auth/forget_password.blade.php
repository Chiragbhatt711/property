@extends('layouts.home_master')
@section('content')
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
                {!! Form::open(array('route' => 'password_reset','method'=>'POST','enctype'=>'multipart/form-data')) !!}
                @csrf
                    <div class="form-group">
                        <input class="form-control" name="email" type="email" placeholder="Email">
                    </div>
                    @error('email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    <div class="form-group">
                        <input type="submit" value="Submit" class="form-control btn button">
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>
@endsection
