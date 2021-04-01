<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->namespace('API\v1')->group(function () {
    
    // Home Api Routes
    Route::get('/popularVillas','HomeController@popularVillas');
    Route::get('/getBanners','HomeController@getBanners');
    Route::get('/discountedVillas','HomeController@discountedVillas');
    Route::get('/economicVillas','HomeController@economicVillas');


    
});
 
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
