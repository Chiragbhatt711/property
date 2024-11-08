<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::middleware('jwt.verify')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1',], function ($router) {
    Route::post('login', 'Api\UserController@login');
    Route::post('register', 'Api\UserController@register');
    Route::post('get-property', 'Api\UserController@getProperty');
    Route::post('get-promoted-property', 'Api\UserController@getPromotedProperty');
    Route::post('get-property-details', 'Api\UserController@getPropertyDetails');
    Route::get('get-city','Api\UserController@getCity');
    Route::post('get-area','Api\UserController@getArea');
    Route::get('get-property-type','Api\UserController@getPropertyType');
    Route::get('get-bhk-type','Api\UserController@getBhkType');
    Route::get('get-poster','Api\UserController@getPoster');
    Route::post('intrested','Api\UserController@intrested');
});

Route::group(['middleware' => ['jwt.verify'], 'prefix' => 'v1',], function ($router) {
    Route::post('logout', 'Api\UserController@logout');
    Route::post('myprofile', 'Api\UserController@myProfile');
});
