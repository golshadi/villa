<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->namespace('API\v1')->group(function () {
    
    // Home Api Routes
    Route::get('/popularVillas','HomeController@popularVillas');
    Route::get('/getBanners','HomeController@getBanners');
    Route::get('/discountedVillas','HomeController@discountedVillas');
    Route::get('/economicVillas','HomeController@economicVillas');


    // Villa Api Routes
    Route::get('/villa/show/{id}','VillaController@show');
    Route::get('/villa/comments/{id}','VillaController@comments');
    Route::get('/villa/images/{id}','VillaController@images');
    
});