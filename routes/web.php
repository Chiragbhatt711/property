<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

// Route::get('phpmyinfo', function () {
//     phpinfo();
// })->name('phpmyinfo');

// Route::get('/clear-cache-all', function () {
//     Artisan::call('cache:clear');
//     Artisan::call('route:clear');
//     Artisan::call('config:clear');
//     Artisan::call('view:clear');
//     phpinfo();
// });

Route::post('/check_admin_user_login', 'Admin\LoginController@checkAdminUserLogin')->name('check_admin_user_login');
Route::post('/check_admin_user_otp', 'Admin\LoginController@checkAdminUserOtp')->name('check_admin_user_otp');

Route::get('/', 'Admin\LoginController@index')->name('login.show');
Route::get('/login', 'Admin\LoginController@index')->name('login');
Route::post('/admin-login', 'Admin\LoginController@login')->name('login_perform');
Route::group(['middleware' => 'auth:admin'], function () {

    Route::get('/logout', 'Admin\LoginController@logout')->name('admin_logout');
    Route::get('dashboard', 'Admin\HomeController@dashboard')->name('dashboard');
    Route::resource('users', 'Admin\UserController');
    Route::get('user_export', 'Admin\UserController@Export')->name('user_export');
    Route::resource('property', 'Admin\PropertyController');
    Route::post('get_city_area', 'Admin\PropertyController@getArea')->name('get_city_area');

    Route::get('/my-profile', 'Admin\UserController@myProfile')->name('my_profile');
    Route::patch('/profile-update/{id}', 'Admin\UserController@profileUpdate')->name('profile_update');

    Route::get('/change-password', 'Admin\ChangePasswordController@index')->name('change_password');
    Route::post('/change-password-action', 'Admin\ChangePasswordController@changePassword')->name('change_password_action');

    Route::get('inquiry','Admin\HomeController@inquiryView')->name('inquiry');
    Route::resource('poster','Admin\PosterController');
    Route::resource('setting','SettingController');
});
