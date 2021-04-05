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
    Route::get('/villa/dates/{id}','VillaController@dates');
    Route::get('/villa/reservedDates/{id}','VillaController@reservedDates');
    Route::get('/villa/similarVillas/{id}','VillaController@similarVillas');


    // User Api Routes
    Route::post('/user/updateInfo','UserController@updateInfo');
    Route::get('/user/reserves','UserController@reserves');
    Route::get('/user/transactions','UserController@transactions');
    Route::get('/user/villas','UserController@villas');
    Route::get('/user/comments/{id}','UserController@comments');
    Route::post('/user/replayComment/{villaId}/{parentId}','UserController@replayComment');
    Route::get('/user/villaDates/{id}','UserController@villaDates');
  
});