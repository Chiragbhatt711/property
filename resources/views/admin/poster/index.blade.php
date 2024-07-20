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
                        <h1>Poster</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a class="btn btn-block btn-primary"
                                    href="{{ route('poster.create') }}">Add New Poster</a></li>
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
                                    <h3 class="card-title">Poster List</h3>
                                    {{-- <a class="btn btn-dark" id="export-to-excel">Export</a> --}}
                                </div>
                            </div>
                            <div class="card-body">

                                @if (\Session::has('success'))
                                    <div>
                                        <p style="color: green;">{!! \Session::get('success') !!}</p>
                                    </div>
                                @endif
                                <table id="" class="table table-bordered table-hover table-responsive-xl">
                                    {{-- <table id="dataTableForAllPages" class="table table-bordered table-hover table-responsive"> --}}
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Image</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($posters) && $posters->count() > 0)
                                        @php
                                            $i=0;
                                        @endphp
                                            @foreach ($posters as $single)
                                            @php
                                                $i++;
                                            @endphp
                                                <tr>
                                                    <td>{{ $i }}</td>
                                                    <td>
                                                        <img style="width:55px !important;height:50px !important;" src="{{ asset('assets/poster_images/'.$single->images) }}" alt="{{$single->images}}">

                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn  dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                Action
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                {{--  <a style="inline-size: max-content;" class="dropdown-item" href="{{route('property.edit', $single->id)}}">Edit</a>  --}}

                                                                <button class="dropdown-item" onclick="delele('{{$single->id}}');">Delete</button>
                                                        </div>
                                                    </td>

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
                                            <th>Image</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                </table>
                                {{-- {!! 'Showing '.$users->firstItem() !!}
                                {!! ' to '.$users->lastItem() !!}
                                {!! ' of '.$users->total().' entries' !!} --}}
                                @if(isset($posters) && $posters)
                                    {!! $posters->appends(request()->input())->links() !!}
                                @endif
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
        $('#message').html("");
        function delele(id) {
            $('#delete_form').attr('action', '{{ url("poster") }}' + '/' + id);
            $('#deleteModal').modal('show');
        }

    </script>
@endsection
