<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\BhkType;
use App\Models\City;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use DB;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $property = Property::leftJoin('property_images','properties.id','property_images.property_id')
                ->select('properties.*', DB::raw('MIN(property_images.id) as first_post_id'), 'property_images.image as image')
                ->paginate(10);
        
        return view('admin.property.index',compact('property'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $propertyType = PropertyType::select('id','name')->get()->pluck('name','id')->toArray();
        $bhk = BhkType::select('id','name')->get()->pluck('name','id')->toArray();
        $city = City::select('id','name')->get()->pluck('name','id')->toArray();
        


        return view('admin.property.create',compact('propertyType','bhk','city'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'area' => 'required',
            'description' => 'required',
            'price' => 'required',
            'property_type' => 'required',
            'status' => 'required',
            'client_name' => 'required',
            'client_phone' => 'required',
        ],[
            'name.required' => 'Please enter property name',
            'address.required' => 'Please enter address',
            'city.required' => 'Please select city',
            'area.required' => 'Please select area',
            'description.required' => 'Please enter description',
            'price.required' => 'Please enter price',
            'property_type.required' => 'Please select property type',
            'status.required' => 'Please select status',
            'client_name.required' => 'Please enter client name',
            'client_phone.required' => 'Please enter client phone number',
        ]);

        // dd($request->file('images'));
        $input = $request->all();

        $create = Property::create($input);

        if($request->hasFile('images')){
            foreach ($request->file('images') as $image){
                // dd($image);
                $fileName = rand().'_'.time() . '.' . $image->extension();

                $destinationPath = str_replace('\\', '/', base_path('public\uploads\property_images/'));
                $image->move($destinationPath, $fileName);

                $imageStore = PropertyImage::create([
                    'property_id' => $create->id,
                    'image' => $fileName,
                ]);
            }
        }
        return redirect()->route('property.index')->with('success','Property created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getArea(Request $request)
    {
        $id = $request->city;

        $area = Area::where('city_id',$id)->select('id','area_name')->get();

        return response()->json($area);
    }
}
