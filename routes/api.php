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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("/login",'App\Http\Controllers\CommonController@login'); 
Route::post("/attendeeRegister",'App\Http\Controllers\CommonController@register'); 
Route::post("/attendeeRegisterCSV",'App\Http\Controllers\CommonController@registerBulk'); 
Route::post("/attendeeRegisterManagers",'App\Http\Controllers\CommonController@registerManagers'); 
Route::post("/updateManagers",'App\Http\Controllers\CommonController@updateManagers'); 
Route::post("/RegisterAdmins",'App\Http\Controllers\CommonController@registerAdmins'); 
Route::post("/updateAdmins",'App\Http\Controllers\CommonController@updateAdmins'); 
Route::post("/createEvent",'App\Http\Controllers\CommonController@events'); 
Route::post("/updateEvent",'App\Http\Controllers\CommonController@updateevents'); 
Route::post("/createRole",'App\Http\Controllers\CommonController@roles'); 
Route::post("/createPermission",'App\Http\Controllers\CommonController@permissions'); 
Route::get("/getdetails",'App\Http\Controllers\CommonController@QrDetails'); 
Route::get("/qrupdate",'App\Http\Controllers\CommonController@QrUpdate'); 