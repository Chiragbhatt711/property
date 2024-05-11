@extends('layouts.user')
@section('content')

<div class="col-lg-12 right-sideinfo">
    <div class="row justify-content-center block-titlewith-desc mb-4">
       <div class="col-xl-4 col-sm-6">          
          <div class="card card-primary">
             <div class="card-header">
                Change Password
             </div>
             <div class="row justify-content-center">
                <!-- left column -->
                <div class="col-md-12">
                    {!! Form::open(array('route' => 'change_password_action','method'=>'POST','enctype'=>'multipart/form-data')) !!}
                   @csrf
                   @if (\Session::has('success'))
                   <div>
                      <p style="color: green;">{!! \Session::get('success') !!}</p>
                   </div>
                   @endif
                   @if (\Session::has('error'))
                   <div>
                      <p style="color: red;">{!! \Session::get('error') !!}</p>
                   </div>
                   @endif
                   <div class="card-body">
                      <div class="form-group mb-3">
                         <label for="exampleInputName">Old password</label>
                         {!! Form::text('old_password', null, array('placeholder' => 'Old password','class' =>'form-control')) !!}
                         @if ($errors->has('old_password'))
                         <p class="text-danger">{{ $errors->first('old_password')}}</p>
                         @endif
                      </div>
                      <div class="form-group mb-3">
                         <label for="exampleInputName">Password</label>
                         {!! Form::text('password', null, array('placeholder' => 'Password','class' =>'form-control')) !!}
                         @if ($errors->has('password'))
                         <p class="text-danger">{{ $errors->first('password')}}</p>
                         @endif
                      </div>
                      <div class="form-group mb-3">
                         <label for="exampleInputName">Confirm Password</label>
                         {!! Form::text('confirm_password', null, array('placeholder' => 'Conform Password','class' =>'form-control')) !!}
                         @if ($errors->has('confirm_password'))
                         <p class="text-danger">{{ $errors->first('confirm_password')}}</p>
                         @endif
                      </div>
                      <button type="submit" class="button">Submit</button>
                   </div>
                   {!! Form::close() !!}
                </div>
             </div>
          </div>
       </div>
    </div>
 </div>


@endsection
