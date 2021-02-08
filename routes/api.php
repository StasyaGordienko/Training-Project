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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/


Route::middleware([\App\Http\Middleware\AuthBasic::class])->group(function () {
    Route::post('/file', 'App\Http\Controllers\FileController@receiveFile');

    Route::get('/file', 'App\Http\Controllers\FileController@getFileInfo');

    Route::delete('/file', 'App\Http\Controllers\FileController@deleteFile');
});
