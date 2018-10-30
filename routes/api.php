<?php

use Illuminate\Http\Request;

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

/*****registration*****/
////params : 
// *name , *email , *password , *password_confirmation
Route::post('/register', 'AuthenticateController@register');

/******login******/
////params: 
// *email , *password 
Route::post('/login', 'AuthenticateController@login');

/******logout******/
Route::get('/logout', 'AuthenticateController@logout');

/*****accept trip*****/
Route::post('/trip/accept', 'DriverController@acceptTrip');

/*****monopolists*****/
Route::get('/monopolists/{time}', 'DriverController@getMonopolists');
