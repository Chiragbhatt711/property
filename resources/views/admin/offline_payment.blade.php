@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Offline Payment</h1>
        </div>
        <div class="col-sm-6">
          {{-- <ol class="breadcrumb float-right">
            <li class="breadcrumb-item"><a  class="btn btn-block btn-primary" href="{{route('admin.faq.create')}}">Add New Faq</a></li>
          </ol> --}}
        </div>
      </div>
    </div>
  </section>
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Offline Payment List</h3>
            </div>
            <div class="card-body">

            <div class="sp_search d-flex align-items-center justify-content-between mb-3">
              <form class="dropdown-block">
                  <div class="row g-3 justify-content-md-end align-items-center">
                    <div class="col-auto">
                        <input type="text" name="start_date" id="start_date" class="form-control datepicker" placeholder="Start Date" value="{{isset($_GET['start_date']) && $_GET['start_date'] ? $_GET['start_date'] : ''}}">
                    </div>
                    <div class="col-auto">
                        <input type="text" name="end_date" id="end_date" class="form-control datepicker" placeholder="End Date" value="{{isset($_GET['end_date']) && $_GET['end_date'] ? $_GET['end_date'] : ''}}">
                    </div>
                    <div class="col-auto">
                        @php
                            $perpage = ['10'=>'10 Per Page','25'=>'25 Per Page','50'=>'50 Per Page','100'=>'100 Per Page'];
                        @endphp
                        {!! Form::select("perpage", $perpage, isset($_GET["perpage"]) && $_GET["perpage"] ? $_GET["perpage"] : null, ['onchange'=>'this.form.submit()','class'=>'form-control']) !!}
                    </div>

                    <div class="col-auto">
                      <div class="input-group flex-nowrap d-flex mt-sm-0 mt-2">
                          <button type="submit" class="input-group-text" id="addon-wrapping"><i class="fas fa-search"></i></button>
                          <input type="search" name="search" value="{{ isset($_GET['search']) && $_GET['search'] ? $_GET['search'] : '' }}" class="form-control" placeholder="Search">
                      </div>
                    </div>
                  </div>
              </form>
            </div>
              @if (\Session::has('success'))
              <div>
                <p style="color: green;">{!! \Session::get('success') !!}</p>
              </div>
              @endif
              <table id="" class="table table-bordered table-hover table-responsive-sm">
                <thead>
                  <tr>
                    <th><a href="javascript:void(0)" onclick="orderBy('id')">No</a></th>
                    <th><a href="javascript:void(0)" onclick="orderBy('user_name')">User Name</a></th>
                    <th><a href="javascript:void(0)" onclick="orderBy('date')">Date</a></th>
                    <th><a href="javascript:void(0)" onclick="orderBy('total')">Total</a></th>
                    <th><a href="javascript:void(0)" onclick="orderBy('status')">Status</a></th>
                    <th>Attachment</th>
                    <th class="w-25">Action</th>
                  </tr>
                </thead>
                <tbody>
                    @if(isset($payment) && $payment)
                    @php $i=0; @endphp
                    @foreach($payment as $single)
                    @php $i++; @endphp
                        <tr>
                            <td>{{$single->id}}</td>
                            <td>{{$single->username}}</td>
                            <td>{{$single->created_at}}</td>
                            <td>
                                @if ($single->status == "Pending")
                                    @switch($single->currency)
                                        @case("INR")
                                            {{'₹ '.number_format((float) $single->amount, 2, '.', '')}}
                                            @break
                                        @case("USD")
                                            {{'$ '.number_format((float) $single->amount, 2, '.', '')}}
                                            @break
                                        @default
                                            {{'₹ '.number_format((float) $single->amount, 2, '.', '')}}
                                    @endswitch
                                @else
                                    @switch($single->currency)
                                        @case("INR")
                                            {{'₹ '.number_format((float) usdToInr($single->amount), 2, '.', '')}}
                                            @break
                                        @case("USD")
                                            {{'$ '.number_format((float) $single->amount, 2, '.', '')}}
                                            @break
                                        @default
                                            {{'₹ '.number_format((float) usdToInr($single->amount), 2, '.', '')}}
                                    @endswitch
                                @endif
                            </td>
                            <td>{{$single->status}}</td>
                            <td>
                                @if(isset($single->attachment) && $single->attachment && \File::exists(str_replace('\\', '/', public_path('uploads/offline_payment/'.$single->attachment))))
                                    <a href="{{ asset('uploads/offline_payment/'.$single->attachment) }}" class="h3 text-warning" target="_blank">
                                        <i class="fas fa-download" aria-hidden="true"></i>
                                    </a>
                                @endif
                            </td>
                            <td>
                                @if ($single->status == "Pending")
                                    <a style="inline-size: max-content;" class="btn btn-primary" href="{{route('admin.offline_payment_approve', $single->id)}}">Approved</a>
                                    <a style="inline-size: max-content;" class="btn btn-primary" href="{{route('admin.offline_payment_reject', $single->id)}}">Reject</a>
                                @endif
                              </td>
                        </tr>
                    @endforeach
                  @endif
                </tbody>
                <tfoot>
                <tr>
                    <th>No</th>
                    <th>User Name</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Attachment</th>
                    <th>Action</th>
                </tr>
                </tfoot>
              </table>
              {{-- {!! 'Showing '.$payment->firstItem() !!}
              {!! ' to '.$payment->lastItem() !!}
              {!! ' of '.$payment->total().' entries' !!} --}}
              {!! $payment->appends(request()->input())->links() !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Modal -->
  <div id="deleteModal" class="modal modal-danger fade" tabindex="-1" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" style="width:55%;">
      <div class="modal-content">
          <div class="modal-header">
            <h6 class="modal-title text-center" id="custom-width-modalLabel">Are you sure want to delete</h6>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i></button>
          </div>
          <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                {!! Form::open(['method' => 'DELETE','style'=>'display:inline','id'=>'delete_form']) !!}
                    <input type="submit" class="btn btn-danger" value="Delete">
                {!! Form::close() !!}
          </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('datatable')
<script>
  $('#message').html("");

  function delele(id)
  {
    $('#delete_form').attr('action','{{ url("ordergatway/faq") }}'+ '/'+id);
    $('#deleteModal').modal('show');
  }

    function orderBy(orderValue){
        var searchParameter = $(location).attr('search');
        var url = new URL($(location).attr('href'));
        var order = url.searchParams.get("order");
        var orderType = url.searchParams.get("orderType");
        searchParameter = searchParameter.replace("&order="+order,'');
        searchParameter = searchParameter.replace("?order="+order,'');
        searchParameter = searchParameter.replace("&orderType="+orderType,'');
        searchParameter = searchParameter.replace("?orderType="+orderType,'');
        var searchUrl = window.location.href;
        var url = searchUrl.split('?')[0];
        if(order == orderValue && orderType == 'ASC')
        {
            window.location = url+(searchParameter ? searchParameter+'&order='+orderValue+'&orderType=DESC'  : '?order='+orderValue+'&orderType=DESC');
        }
        else
        {
            window.location = url+(searchParameter ? searchParameter+'&order='+orderValue+'&orderType=ASC'  : '?order='+orderValue+'&orderType=ASC');
        }
    }
</script>
@endsection
