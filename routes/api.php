<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth',
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    Route::post('login', 'AuthController@login');
    // Route::post('logout', 'AuthController@logout');
    // Route::post('refresh', 'AuthController@refresh');
    Route::get('me', 'AuthController@me');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'ghibli',
    'namespace' => 'App\Http\Controllers\Ghibli',
], function ($router) {
    // Route::get('/', 'DataController@index');
    // Route::get('/film', 'FilmController@index');
    Route::apiResource('film', FilmController::class)->only(['index', 'show']);
    Route::get('/film/detail/{id}', 'FilmController@detail');
});

// Route::get('/test', function() {
//     // return Music::all();
//     // $model_music = new Music();
//     return  User::all();
// });
