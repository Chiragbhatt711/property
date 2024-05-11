@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                </div>
                {{-- <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a class="btn btn-block btn-primary"
                                href="{{ route('admin.category.index') }}">Back</a></li>
                    </ol>
                </div> --}}
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Change Password</h3>
                        </div>
                        {!! Form::open(array('route' => 'admin.change_password_action','method'=>'POST','enctype'=>'multipart/form-data')) !!}
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
                                <div class="form-group">
                                    <label for="exampleInputName">Old password</label>
                                    {!! Form::text('old_password', null, array('placeholder' => 'Old password','class' =>'form-control')) !!}
                                    @if ($errors->has('old_password'))
                                        <p class="text-danger">{{ $errors->first('old_password')}}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName">Password</label>
                                    {!! Form::text('password', null, array('placeholder' => 'Password','class' =>'form-control')) !!}
                                    @if ($errors->has('password'))
                                        <p class="text-danger">{{ $errors->first('password')}}</p>
                                    @endif
                                </div>
                                <div class="form-group m-0">
                                    <label for="exampleInputName">Conform Password</label>
                                    {!! Form::text('confirm_password', null, array('placeholder' => 'Conform Password','class' =>'form-control')) !!}
                                    @if ($errors->has('confirm_password'))
                                        <p class="text-danger">{{ $errors->first('confirm_password')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
