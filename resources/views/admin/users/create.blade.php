@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a class="btn btn-block btn-primary"
                                href="{{ route('admin.users.index') }}">Back</a></li>
                    </ol>
                </div>
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
                            <h3 class="card-title">Add User</h3>
                        </div>
                        {!! Form::open(array('route' => 'admin.users.store','method'=>'POST','enctype'=>'multipart/form-data')) !!}
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="exampleInputName">User Name</label>
                                    {!! Form::text('username', null, array('placeholder' => 'User Name','class' =>'form-control')) !!}
                                    @if ($errors->has('username'))
                                        <p class="text-danger">{{ $errors->first('username')}}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName">E-mail</label>
                                    {!! Form::text('email', null, array('placeholder' => 'E-mail','class' =>'form-control')) !!}
                                    @if ($errors->has('email'))
                                        <p class="text-danger">{{ $errors->first('email')}}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName">Phone</label>
                                    {!! Form::text('phone', null, array('placeholder' => 'Phone','class' =>'form-control')) !!}
                                    @if ($errors->has('phone'))
                                        <p class="text-danger">{{ $errors->first('phone')}}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName">Password</label>
                                    {!! Form::text('password', null, array('placeholder' => 'Password','class' =>'form-control')) !!}
                                    @if ($errors->has('password'))
                                        <p class="text-danger">{{ $errors->first('password')}}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName">Conform Password</label>
                                    {!! Form::text('confirm_password', null, array('placeholder' => 'Conform Password','class' =>'form-control')) !!}
                                    @if ($errors->has('confirm_password'))
                                        <p class="text-danger">{{ $errors->first('confirm_password')}}</p>
                                    @endif
                                </div>
                                <div class="form-group m-0">
                                    <label for="correct_answer">User Status</label>
                                    <br>
                                    <input type="radio"  name="status" id="activate" value="Activate" placeholder="" checked>
                                    <label for="activate">Active</label>
                                    <input type="radio"  name="status" id="deactivate" value="Deactivate" placeholder="">
                                    <label for="deactivate">Deactive</label>
                                    @if ($errors->has('status'))
                                    <p class="text-danger">{{ $errors->first('status')}}
                                    </p>
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
