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
                                    'route' => ['admin.setting.update', $setting->id],
                                    'enctype' => 'multipart/form-data',
                                ]) !!}
                            @else
                                {!! Form::open(['route' => 'admin.setting.store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
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
                                        <h4>Currency conversation rate setting</h4>
                                        <label for="exampleInputName">Conversion USD to INR</label>
                                        {!! Form::text('usd_to_inr', null, ['class' => 'form-control']) !!}
                                        @if ($errors->has('usd_to_inr'))
                                            <p class="text-danger">{{ $errors->first('usd_to_inr') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group mb-5">
                                    <div class="form-group">
                                        <h4>Reffaral settings</h4>
                                        <label for="exampleInputName">Level 1 earn in %</label>
                                        {!! Form::text('level_1', null, ['class' => 'form-control']) !!}
                                        @if ($errors->has('level_1'))
                                            <p class="text-danger">{{ $errors->first('level_1') }}</p>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputName">Level 2 earn in %</label>
                                        {!! Form::text('level_2', null, ['class' => 'form-control']) !!}
                                        @if ($errors->has('level_2'))
                                            <p class="text-danger">{{ $errors->first('level_2') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group mb-5">
                                    <div class="form-group">
                                        <h4>Deposit setting</h4>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="exampleInputName">Debit or credit card bonus in %</label>
                                                {!! Form::text('debit_credit_bonus', null, ['class' => 'form-control']) !!}
                                                @if ($errors->has('debit_credit_bonus'))
                                                    <p class="text-danger">{{ $errors->first('debit_credit_bonus') }}</p>
                                                @endif
                                            </div>
                                            <div class="col-md-6 mt-4">
                                                <label for="application_to_international_for_debit_credit">Applicable to
                                                    international ?</label>
                                                {!! Form::checkbox('application_to_international_for_debit_credit') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="exampleInputName">Crypto bonus in %</label>
                                                {!! Form::text('crypto_discount', null, ['class' => 'form-control']) !!}
                                                @if ($errors->has('debit_credit_bonus'))
                                                    <p class="text-danger">{{ $errors->first('debit_credit_bonus') }}</p>
                                                @endif
                                            </div>
                                            <div class="col-md-6 mt-4">
                                                <label for="application_to_international_for_crypto">Applicable to
                                                    international ?</label>
                                                {!! Form::checkbox('application_to_international_for_crypto') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputName">Paytm bonus in %</label>
                                        {!! Form::text('payment_discount', null, ['class' => 'form-control']) !!}
                                        @if ($errors->has('payment_discount'))
                                            <p class="text-danger">{{ $errors->first('payment_discount') }}</p>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputName">Minimum deposit</label>
                                        {!! Form::text('minimum_deposit', null, ['class' => 'form-control']) !!}
                                        @if ($errors->has('minimum_deposit'))
                                            <p class="text-danger">{{ $errors->first('minimum_deposit') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <div class="form-group">
                                        <h4>Invoice setting</h4>
                                        <label for="exampleInputName">Prefix</label>
                                        {!! Form::text('prefix', null, ['class' => 'form-control']) !!}
                                        @if ($errors->has('prefix'))
                                            <p class="text-danger">{{ $errors->first('prefix') }}</p>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputName">Invoice number</label>
                                        {!! Form::text('invoice_number', null, ['class' => 'form-control']) !!}
                                        @if ($errors->has('invoice_number'))
                                            <p class="text-danger">{{ $errors->first('invoice_number') }}</p>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputName">Postfix</label>
                                        {!! Form::text('postfix', null, ['class' => 'form-control']) !!}
                                        @if ($errors->has('postfix'))
                                            <p class="text-danger">{{ $errors->first('postfix') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <div class="form-group m-0">
                                        <h4>Signup Bonus</h4>
                                        <label for="exampleInputName">signup bonus</label>
                                        {!! Form::radio('sign_up', '1', false, ['id' => 'bonus_yes']) !!}
                                        <label for="bonus_yes">Yes</label>
                                        {!! Form::radio('sign_up', '0', true, ['id' => 'bonus_no']) !!}
                                        <label for="bonus_no">No</label>
                                        @error('sign_up')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group" id="div_bonus_yes">
                                        <label for="exampleInputName">Enter amount</label>
                                        {!! Form::text('sign_up_amount', null, [
                                            'placeholder' => 'Enter amount',
                                            'class' => 'form-control',
                                            'id' => 'bonus_amount',
                                        ]) !!}
                                        @error('sign_up_amount')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
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
