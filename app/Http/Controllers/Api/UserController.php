<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;
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
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors(), 'Validation Error', 422);
        }

        $input = $request->all();
        $input['password'] = Hash::make('11111');
        $user = User::create($input);

        $success = [
            'message' => 'Register Successfully',
        ];
        return $this->sendResponse($success, 'successful register', 200);
    }

    public function getProperty(Request $request)
    {
        $properties = Property::leftJoin('property_images','properties.id','property_images.property_id')
                ->select('properties.*', DB::raw('MIN(property_images.id) as first_post_id'), 'property_images.image as image')
                ->get();

        $properties->transform(function ($property) {
            if ($property->image) {
                $imageUrl = asset('uploads/property_images/' . $property->image);
                $property->image_url = $imageUrl;
            } else {
                $property->image_url = null;
            }

            return $property;
        });

        $success = "success";
        return $this->sendResponse($success, $properties, 200);
    }
}
