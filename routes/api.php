<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->namespace('API\v1')->group(function () {

    // Home Api Routes
    Route::get('/popularVillas', 'HomeController@popularVillas');
    Route::get('/getBanners', 'HomeController@getBanners');
    Route::get('/discountedVillas', 'HomeController@discountedVillas');
    Route::get('/economicVillas', 'HomeController@economicVillas');


    // Villa Api Routes
    Route::prefix('villa')->group(function () {
        Route::get('show/{id}', 'VillaController@show');
        Route::get('comments/{id}', 'VillaController@comments');
        Route::get('images/{id}', 'VillaController@images');
        Route::get('dates/{id}', 'VillaController@dates');
        Route::get('reservedDates/{id}', 'VillaController@reservedDates');
        Route::get('similarVillas/{id}', 'VillaController@similarVillas');
        Route::post('store','VillaController@store');
        Route::post('img/{id}','VillaController@img');
        Route::get('test', function () {
            return view('welcome');
        });
    });

    // User Api Routes
    Route::prefix('user')->group(function () {
        Route::post('updateInfo', 'UserController@updateInfo');
        Route::get('reserves', 'UserController@reserves');
        Route::get('transactions', 'UserController@transactions');
        Route::get('villas', 'UserController@villas');
        Route::get('comments/{id}', 'UserController@comments');
        Route::post('replayComment/{villaId}/{parentId}', 'UserController@replayComment');
        Route::get('villaDates/{id}', 'UserController@villaDates');
        Route::post('changeDatesCost/{id}', 'UserController@changeDatesCost');
        Route::post('changeDatesStatus/{id}', 'UserController@changeDatesStatus');
        Route::get('reservationsRequested/{id}', 'UserController@reservationsRequested');
        Route::post('changeReserveStatus/{id}', 'UserController@changeReserveStatus');
        Route::post('withdrawal', 'UserController@withdrawal');
        Route::post('updateVilla', 'UserController@updateVilla');
        Route::post('login','AuthController@login');
        Route::post('register','AuthController@register');
        Route::post('sendSmsAuth','AuthController@sendSmsAuth');
        Route::post('sendSmsAuth','AuthController@sendSmsAuth');
        Route::post('verifySmsCode','AuthController@verifySmsCode');
        Route::post('addToFavorite','AuthController@addToFavorite');
        Route::post('removeFromFavorite','AuthController@removeFromFavorite');
        
    });


    // Search Api Routes
    Route::get('search','SearchController@search');
    Route::get('doSearch','SearchController@doSearch');
    Route::get('testSearch','SearchController@testSearch');


    // Reservation Api Routes
    Route::post('reserveRequest','ReservationController@reserveRequest');
    
});
