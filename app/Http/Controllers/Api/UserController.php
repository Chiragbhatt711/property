<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\BhkType;
use App\Models\City;
use App\Models\Intrested;
use App\Models\PosterImage;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Validator;
use Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;

class UserController extends Controller
{
    public function sendResponse($data, $message, $status = 200)
    {
        $response = [
            'data' => $data,
            'message' => $message
        ];

        return response()->json($response, $status);
    }

    public function sendError($errorData, $message, $status = 500)
    {
        $response = [];
        $response['message'] = $message;
        if (!empty($errorData)) {
            $response['data'] = $errorData;
        }

        return response()->json($response, $status);
    }

    public function login(Request $request)
    {
        $input = $request->only('email', 'password');

        $validator = Validator::make($input, [
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors(), 'Validation Error', 422);
        }

        try {
            // this authenticates the user details with the database and generates a token

            if (!$token = JWTAuth::attempt(['email'=> $request->email,'password'=>$request->password,'user_type'=>"0"])) {
                return $this->sendError([], "invalid login credentials", 400);
            }
        } catch (JWTException $e) {
            // return $this->sendError([], $e->getMessage(), 500);
        }

        $success = [
            'token' => $token,
        ];
        return $this->sendResponse($success, 'successful login', 200);
    }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'email' => 'required|unique:users,email',
            'phone'=>  'required',
            'password' => 'required|min:8',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors(), 'Validation Error', 422);
        }

        $input = $request->all();
        if($request->password){
            $input['password'] = Hash::make($request->password);
        }
        $user = User::create($input);

        $success = [
            'message' => 'Register Successfully',
        ];
        return $this->sendResponse($success, 'successful register', 200);
    }

    public function getProperty(Request $request)
    {

        $properties = Property::leftJoin('property_images', 'properties.id', '=', 'property_images.property_id')
        ->leftjoin('cities','properties.city','=','cities.id')
        ->leftjoin('areas','properties.area','=','areas.id')
        ->leftjoin('bhk_types','properties.bhk','=','bhk_types.id');
        // if(isset($request->))

	    $properties = $properties->select('properties.*','cities.name as city_name','areas.area_name','bhk_types.name as bhk', DB::raw('GROUP_CONCAT(property_images.image) as images'))
        ->groupBy('properties.id')
	    ->get();

        $setting = Setting::first();

        // Add URL to each image
        foreach ($properties as $property) {
            $images = explode(',', $property->images);
            $imageUrls = [];
            foreach ($images as $image) {
                if($image){
                    $imageUrls[] = asset('uploads/property_images/' . $image); // Modify the path as per your setup
                }
            }
            $property->images = $imageUrls;
            $property->city = $property->city_name;
            $property->area = $property->area_name;
            $property->address = $property->address.', '. $property->area_name.', '.$property->city_name;
            $property->whatsapp_number = $setting->wp_number;
            $property->phone_number = $setting->phone_number;

            $address = $property->id." ".$property->name;
            $wpMessage = $setting->wp_message;
            $wpMessage = str_replace('[property_name]',$address,$wpMessage);
            $property->whatsapp_message = $wpMessage;
        }

        $success = "success";
        return $this->sendResponse($properties, $success, 200);
    }

    public function getPromotedProperty()
    {
        $properties = Property::leftJoin('property_images', 'properties.id', '=', 'property_images.property_id')
            ->leftjoin('cities','properties.city','=','cities.id')
            ->leftjoin('areas','properties.area','=','areas.id')
            ->where('properties.promoted', 1)
            ->select('properties.*','cities.name as city_name','areas.area_name', DB::raw('GROUP_CONCAT(property_images.image) as images'))
            ->groupBy('properties.id')
            ->get();

        $setting = Setting::first();
        // Add URL to each image
        foreach ($properties as $property) {
            $images = explode(',', $property->images);
            $imageUrls = [];
            foreach ($images as $image) {
                if($image){
                    $imageUrls[] = asset('uploads/property_images/' . $image); // Modify the path as per your setup
                }
            }
            $property->images = $imageUrls;
            $property->city = $property->city_name;
            $property->area = $property->area_name;
            $property->address = $property->address.', '. $property->area_name.', '.$property->city_name;
            $property->whatsapp_number = $setting->wp_number;
            $property->phone_number = $setting->phone_number;

            $address = $property->id." ".$property->name;
            $wpMessage = $setting->wp_message;
            $wpMessage = str_replace('[property_name]',$address,$wpMessage);
            $property->whatsapp_message = $wpMessage;
        }

        $success = "success";
        return $this->sendResponse($properties, $success, 200);
    }

    public function getPropertyDetails(Request $request)
    {
        $input = $request->only('id');

        $validator = Validator::make($input, [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors(), 'Validation Error', 422);
        }
        $id = $request->id;
        if($id){
            $properties = Property::leftJoin('property_images', 'properties.id', '=', 'property_images.property_id')
                ->leftjoin('cities','properties.city','=','cities.id')
                ->leftjoin('areas','properties.area','=','areas.id')
                ->where('properties.id',$id)
                ->select('properties.*','cities.name as city_name','areas.area_name', 'property_images.id as image_id', 'property_images.image')
                ->orderBy('properties.id') // Optionally order by property ID for consistency
                ->get();

            // Group properties by ID and collect images for each property
            $groupedProperties = $properties->groupBy('id')->map(function ($group) {
                $property = $group->first(); // Get the first property in the group

                $setting = Setting::first();
                // Append all images associated with the property
                $property->images = $group->map(function ($item) {
                    return [
                        'id' => $item->image_id,
                        'url' => asset('uploads/property_images/' . $item->image)
                    ];
                })->all();

                // Determine the first image (based on the minimum image ID)
                $property->first_image = $group->min('image_id');

                $property->city = $property->city_name;
                $property->area = $property->area_name;
                $property->address = $property->address.', '. $property->area_name.', '.$property->city_name;
                $property->whatsapp_number = $setting->wp_number;
                $property->phone_number = $setting->phone_number;

                $address = $property->id." ".$property->name;
                $wpMessage = $setting->wp_message;
                $wpMessage = str_replace('[property_name]',$address,$wpMessage);
                $property->whatsapp_message = $wpMessage;

                return $property;
            })->values();

            $success = "success";
            return $this->sendResponse($groupedProperties, $success, 200);
        }
    }

    public function intrested(Request $request)
    {
        $input = $request->only('property_id','user_name','mobile_number');

        $validator = Validator::make($input, [
            'property_id' => 'required',
            'user_name' => 'required',
            'mobile_number' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors(), 'Validation Error', 422);
        }

        $insert = Intrested::create($input);
        $success = "Submit successfully";
        $data = [];
        return $this->sendResponse($data, $success, 200);
    }

    public function getCity()
    {
        $data = City::get();
        $success="Success";
        return $this->sendResponse($data, $success, 200);
    }

    public function getArea(Request $request)
    {
        $id = $request->id;
        $data = [];
        if($id){
            $data = Area::where('city_id',$id)->get();
        }
        $success="Success";
        return $this->sendResponse($data, $success, 200);
    }

    public function getPropertyType()
    {
        $data = PropertyType::get();
        $success="Success";
        return $this->sendResponse($data, $success, 200);
    }

    public function getBhkType()
    {
        $data = BhkType::get();
        $success="Success";
        return $this->sendResponse($data, $success, 200);
    }

    public function getPoster()
    {
        $poster = PosterImage::get();
        $data=[];
        if($poster){
            foreach($poster as $value){
                $data[]= ['image'=>asset('assets/poster_images/'.$value->images)];
            }
        }
        $success="Success";
        return $this->sendResponse($data, $success, 200);
    }
}
