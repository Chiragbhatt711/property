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
                                href="{{ route('property.index') }}">Back</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Add Property</h3>
                        </div>
                        {!! Form::model($property, ['method' => 'PATCH','route' => ['property.update',$property->id],'enctype' => 'multipart/form-data']) !!}
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="exampleInputName">Propert Name</label>
                                    {!! Form::text('name', null, array('placeholder' => 'Propert Name','class' =>'form-control')) !!}
                                    @if ($errors->has('name'))
                                        <p class="text-danger">{{ $errors->first('name')}}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName">Address</label>
                                    {!! Form::text('address', null, array('placeholder' => 'Address','class' =>'form-control')) !!}
                                    @if ($errors->has('address'))
                                        <p class="text-danger">{{ $errors->first('address')}}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName">City</label>
                                    {!! Form::select('city',$city, null, array('placeholder' => 'Please select','class' =>'form-control','id'=>'city')) !!}
                                    @if ($errors->has('city'))
                                        <p class="text-danger">{{ $errors->first('city')}}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName">Area</label>
                                    {!! Form::select('area',[], null, array('placeholder' => 'Please select','class' =>'form-control','id'=>'area')) !!}
                                    @if ($errors->has('area'))
                                        <p class="text-danger">{{ $errors->first('area')}}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName">Description</label>
                                    {!! Form::textarea('description', null, array('placeholder' => 'Description','class' =>'form-control')) !!}
                                    @if ($errors->has('description'))
                                        <p class="text-danger">{{ $errors->first('description')}}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName">Price</label>
                                    {!! Form::text('price', null, array('placeholder' => 'Price','class' =>'form-control')) !!}
                                    @if ($errors->has('price'))
                                        <p class="text-danger">{{ $errors->first('price')}}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName">Propert type</label>
                                    {!! Form::select('property_type',$propertyType, $property->property_type, array('placeholder' => 'Please select','class' =>'form-control')) !!}
                                    @if ($errors->has('property_type'))
                                        <p class="text-danger">{{ $errors->first('property_type')}}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName">BHK</label>
                                    {!! Form::select('bhk',$bhk, $property->bhk, array('placeholder' => 'Please select','class' =>'form-control')) !!}
                                    @if ($errors->has('bhk'))
                                        <p class="text-danger">{{ $errors->first('bhk')}}</p>
                                    @endif
                                </div>
                                <div class="form-group m-0">
                                    <label for="correct_answer">Status</label>
                                    <br>
                                    <input type="radio"  name="status" id="activate" value="1" placeholder="" {{ $property->status == 1 ? 'checked':'' }} >
                                    <label for="activate">Active</label>
                                    <input type="radio"  name="status" id="deactivate" value="0" placeholder="" {{ $property->status == 0 ? 'checked':'' }}>
                                    <label for="deactivate">Deactive</label>
                                    @if ($errors->has('status'))
                                    <p class="text-danger">{{ $errors->first('status')}}
                                    </p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName">Images</label>
                                    <input type="file" name="images[]" id="images" multiple>
                                    @if ($errors->has('images'))
                                        <p class="text-danger">{{ $errors->first('images')}}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="parking_two_wheel">
                                        <input type="checkbox" value="1" name="parking_two_wheel" id="parking_two_wheel" {{ $property->parking_two_wheel == 1 ? 'checked':'' }}>
                                    Tow wheel Parking</label>
                                    <label for="parking_for_wheel">
                                        <input type="checkbox" value="1" name="parking_for_wheel" id="parking_for_wheel" {{ $property->parking_for_wheel == 1 ? 'checked':'' }}>
                                    For wheel Parking</label>
                                    <label for="electricity">
                                        <input type="checkbox" value="1" name="electricity" id="electricity" {{ $property->electricity == 1 ? 'checked':'' }}>
                                    Electricity</label>
                                    <label for="furniture">
                                        <input type="checkbox" value="1" name="furniture" id="furniture" {{ $property->furniture == 1 ? 'checked':'' }}>
                                    Furniture</label>
                                    <label for="other_electric_accessories">
                                        <input type="checkbox" value="1" name="other_electric_accessories" id="other_electric_accessories" {{ $property->other_electric_accessories == 1 ? 'checked':'' }}>
                                    Other electric accessories</label>
                                </div>
                                <div class="form-group">
                                    <label for="verified">
                                        <input type="checkbox" value="1" name="verified" id="verified" {{ $property->verified == 1 ? 'checked':'' }}>
                                    Is Verified</label>
                                </div>
                                <div class="form-group">
                                    <label for="promoted">
                                        <input type="checkbox" value="1" name="promoted" id="promoted" {{ $property->promoted == 1 ? 'checked':'' }}>
                                    Is Promoted</label>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName">Client Name</label>
                                    {!! Form::text('client_name', null, array('placeholder' => 'Client Name','class' =>'form-control')) !!}
                                    @if ($errors->has('client_name'))
                                        <p class="text-danger">{{ $errors->first('client_name')}}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName">Client Phone</label>
                                    {!! Form::text('client_phone', null, array('placeholder' => 'Client Phone','class' =>'form-control')) !!}
                                    @if ($errors->has('client_phone'))
                                        <p class="text-danger">{{ $errors->first('client_phone')}}</p>
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
@section('datatable')
<script>
    $(document).ready(function(){
        $('#city').trigger('change');
    })
    $('#city').change(function(){
        var areaId = '{{ $property->area }}';
        $.ajax({
            url: "{{ route('get_city_area') }}",
            type:'POST',
            data:{
                    '_token' : $('meta[name="csrf-token"]').attr('content'),
                    city:$('#city').val(),
            },
            success:function(data) {
                let html ="<option value=''>Please select</option>";
                $.each(data, function(key, value){
                    var selected = "";
                    if(value.id == areaId){
                        selected = "selected";
                    }
                    html += "<option value='"+value.id+"' "+selected+" >"+value.area_name+"</option>";
                })
                $("#area").html(html);
            }
        });
    });
</script>
@endsection
