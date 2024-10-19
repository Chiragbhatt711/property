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
                    {{-- <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a class="btn btn-block btn-primary"
                                href="{{ route('admin.category.index') }}">Back</a></li>
                    </ol> --}}
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
                            <h3 class="card-title">My Profile</h3>
                        </div>
                        {!! Form::model($user, ['method' => 'PATCH','route' => ['profile_update',$user->id],'enctype' => 'multipart/form-data']) !!}
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
                                    <label for="exampleInputName">User Name</label>
                                    {!! Form::text('username', null, array('placeholder' => 'User Name','class' =>'form-control')) !!}
                                    @if ($errors->has('username'))
                                        <p class="text-danger">{{ $errors->first('username')}}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName">E-mail</label>
                                    {!! Form::text('email', null, array('placeholder' => 'E-mail','class' =>'form-control','value' => '$user')) !!}
                                    @if ($errors->has('email'))
                                        <p class="text-danger">{{ $errors->first('email')}}</p>
                                    @endif
                                </div>
                                <div class="form-group m-0">
                                    <label for="exampleInputName">Phone</label>
                                    {!! Form::text('phone', null, array('placeholder' => 'Phone','class' =>'form-control')) !!}
                                    @if ($errors->has('phone'))
                                        <p class="text-danger">{{ $errors->first('phone')}}</p>
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
