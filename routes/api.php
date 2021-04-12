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
        Route::get('comments/{id}', 'CommentController@getVillaComments');
        Route::get('images/{id}', 'VillaController@images');
        Route::get('dates/{id}', 'VillaController@dates');
        Route::get('reservedDates/{id}', 'VillaController@reservedDates');
        Route::get('similarVillas/{id}', 'VillaController@similarVillas');
        Route::post('store','VillaController@store');
    });


    // User Api Routes
    Route::middleware('auth:api')->prefix('user')->group(function () {
        Route::get('getUserInfo','UserController@getUserInfo');
        Route::post('updateInfo', 'UserController@updateInfo');
        Route::get('reserves', 'UserController@reserves');
        Route::get('transactions', 'UserController@transactions');
        Route::get('villas', 'UserController@villas');
        Route::get('editVilla/{id}', 'UserController@editVilla');
        Route::get('getUserVillaComments/{id}', 'CommentController@getUserVillaComments');
        Route::post('replayComment/{villaId}/{parentId}', 'CommentController@replayComment');
        Route::post('addComment/{villaId}', 'CommentController@addComment');
        Route::get('villaDates/{id}', 'UserController@villaDates');
        Route::post('changeDatesCost/{id}', 'UserController@changeDatesCost');
        Route::post('changeDatesStatus/{id}', 'UserController@changeDatesStatus');
        Route::get('allReservationsRequested', 'UserController@allReservationsRequested');
        Route::get('reservationsRequested/{id}', 'UserController@reservationsRequested');
        Route::post('changeReserveStatus/{id}', 'UserController@changeReserveStatus');
        Route::post('withdrawal', 'UserController@withdrawal');
        Route::get('favorites', 'FavoriteController@getFavorites');
        Route::post('addToFavorite','FavoriteController@addToFavorite');
        Route::post('removeFromFavorite','FavoriteController@removeFromFavorite');
        Route::get('getFinancialReports','UserController@getFinancialReports');
        Route::post('setFinancialReports','UserController@setFinancialReports');
    });
    Route::get('villaIncome/{id}','UserController@villaIncome');

    Route::post('login','AuthController@login');
    Route::post('register','AuthController@register');
    Route::post('sendRegisterSms','AuthController@sendRegisterSms');
    Route::post('sendNormalSms','AuthController@sendNormalSms');
    Route::post('verifySmsCode','AuthController@verifySmsCode');


    // Search Api Routes
    Route::get('search','SearchController@search');
    Route::get('doSearch','SearchController@doSearch');


    // Reservation Api Routes
    Route::post('reserveRequest','ReservationController@reserveRequest');

    // Factor Api Routes
    Route::get('factor/{id}', 'FactorController@getFactor'); // id ==> Reserve id

    // Pay Api Routes
    Route::post('/request/pay', 'PaymentController@pay');
    Route::get('/payir/callback', 'PaymentController@verify');
    Route::post('/getPayStatus','PaymentController@getPayStatus');


});