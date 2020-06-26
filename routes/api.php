<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/login','UserController@login');


Route::get('/data', function () {
    return Carbon::now();
});

Route::resource('/leitura','ArduinoController');

Route::group(['prefix' => '/', 'middleware' => 'auth:api'], function () {

    Route::resource('/equipamento','EquipamentoController');

    Route::put('/solenoide/{id}','ArduinoController@update');

});

Route::resource('/solenoide','ArduinoController');


Route::get('/status/solenoide/{id}','ArduinoController@status');