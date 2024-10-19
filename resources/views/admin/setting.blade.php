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
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-10">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Setting</h3>
                            </div>
                            @if (isset($setting) && $setting)
                                {!! Form::model($setting, [
                                    'method' => 'PATCH',
                                    'route' => ['setting.update', $setting->id],
                                    'enctype' => 'multipart/form-data',
                                ]) !!}
                            @else
                                {!! Form::open(['route' => 'setting.store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
                            @endif
                            @csrf
                            <div class="card-body">
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
                                <div class="form-group mb-5">
                                    <div class="form-group">
                                        <label for="wp_number">Whatsapp number</label>
                                        {!! Form::text('wp_number', null, ['class' => 'form-control']) !!}
                                        @if ($errors->has('wp_number'))
                                            <p class="text-danger">{{ $errors->first('wp_number') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group mb-5">
                                    <div class="form-group">
                                        <label for="phone_number">Phone number</label>
                                        {!! Form::text('phone_number', null, ['class' => 'form-control']) !!}
                                        @if ($errors->has('phone_number'))
                                            <p class="text-danger">{{ $errors->first('phone_number') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group mb-5">
                                    <div class="form-group">
                                        <label for="wp_message">Whatsapp message</label>
                                        {!! Form::textarea('wp_message', null, ['class' => 'form-control']) !!}
                                        <p>Enter "[property_name]" for replace to inquiry of property name</p>
                                        @if ($errors->has('wp_message'))
                                            <p class="text-danger">{{ $errors->first('wp_message') }}</p>
                                        @endif
                                    </div>
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
@section('datatable')
    <script>
        $(document).ready(function() {
            $('#div_bonus_yes').hide();

            if ($('#bonus_yes').is(':checked')) {
                $('#div_bonus_yes').show();
            }
        });

        $('#bonus_yes').on('change', function() {
            if ($(this).is(':checked')) {
                $('#div_bonus_yes').show();
            }
        });

        $('#bonus_no').on('change', function() {
            if ($(this).is(':checked')) {
                $('#bonus_amount').val(0);
                $('#div_bonus_yes').hide();
            }
        });
    </script>
@endsection
