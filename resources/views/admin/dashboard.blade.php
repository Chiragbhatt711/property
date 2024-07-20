@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard</h1>
                </div>
                <div class="col-sm-6">
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Dashboard</h3>
                        </div>
                        <div class="card-body">
                            <div class="user_value">
                                <div class="box">
                                    <div class="bot">
                                        <div class="bar_box">
                                            <a href="{{ route('property.index') }}" class="box">
                                                <strong>{{ totalPeoperty() }}</strong>
                                                <span>Total Property</span>
                                            </a>
                                            <a href="{{ route('inquiry') }}" class="box">
                                                <strong>{{ TodayInquery() }}</strong>
                                                <span>Today's Inquery</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
