@extends('layouts.admin')
@section('content')
    <style>
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Users</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a class="btn btn-block btn-primary"
                                    href="{{ route('users.create') }}">Add New User</a></li>
                        </ol>
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
                                <div class="d-flex align-items-center justify-content-between">
                                    <h3 class="card-title">Users List</h3>
                                    <a class="btn btn-dark" id="export-to-excel">Export</a>
                                </div>
                            </div>
                            <div class="card-body">

                                <div class="sp_search d-flex align-items-center justify-content-between mb-3">
                                    <form class="dropdown-block">
                                        <div class="row g-3 justify-content-md-end align-items-center">
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
                                <table id="" class="table table-bordered table-hover table-responsive-xl">
                                    {{-- <table id="dataTableForAllPages" class="table table-bordered table-hover table-responsive"> --}}
                                    <thead>
                                        <tr>
                                            <th><a href="javascript:void(0)" onclick="orderBy('no')">No</a></th>
                                            <th><a href="javascript:void(0)" onclick="orderBy('username')">User Name</a></th>
                                            <th><a href="javascript:void(0)" onclick="orderBy('email')">E-mail</a></th>
                                            <th><a href="javascript:void(0)" onclick="orderBy('phone')">Phone</a></th>
                                            {{-- <th><a href="javascript:void(0)" onclick="orderBy('status')">Status</a></th> --}}
                                            {{-- <th>Action</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($users) && $users->count() > 0)
                                            @foreach ($users as $single)
                                                <tr>
                                                    <td>{{$single->id}}</td>
                                                    <td>{{$single->username}}</td>
                                                    <td>{{$single->email}}</td>
                                                    <td>{{$single->phone}}</td>
                                                    @php
                                                        $status = 'Deactive';
                                                        if (strtolower($single->status) == 'activate') {
                                                            $status = 'Active';
                                                        }
                                                    @endphp
                                                    {{-- <td>
                                                        <div class="dropdown">
                                                            <button class="btn  dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                Action
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                <a style="inline-size: max-content;" class="dropdown-item" href="{{route('users.edit', $single->id)}}">Edit</a>

                                                                <button class="dropdown-item" onclick="delele('{{$single->id}}');">Delete</button>
                                                        </div>
                                                    </td> --}}

                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="11" class="text-center record-not-found">No records found</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>User Name</th>
                                            <th>E-mail</th>
                                            <th>Phone</th>
                                            {{-- <th>Status</th> --}}
                                            {{-- <th>Action</th> --}}
                                        </tr>
                                    </tfoot>
                                </table>
                                {{-- {!! 'Showing '.$users->firstItem() !!}
                                {!! ' to '.$users->lastItem() !!}
                                {!! ' of '.$users->total().' entries' !!} --}}
                                {!! $users->appends(request()->input())->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Modal -->
        <div id="deleteModal" class="modal modal-danger fade" tabindex="-1" role="dialog"
            aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog" style="width:55%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title text-center" id="custom-width-modalLabel">Are you sure want to delete</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                                class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                        {!! Form::open(['method' => 'DELETE', 'style' => 'display:inline', 'id' => 'delete_form']) !!}
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
        $( document ).ready(function() {
            var searchParameter = $(location).attr('search');
            $('#export-to-excel').attr('href','{{ route("user_export") }}'+(searchParameter ? searchParameter : ''));
        });
        $('#message').html("");
        function delele(id) {
            $('#delete_form').attr('action', '{{ url("ordergatway/users") }}' + '/' + id);
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
