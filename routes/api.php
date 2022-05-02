<?php

use App\Http\Controllers\Api\v1\ArticlesController;
use App\Http\Controllers\Api\v1\UsersController;
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

//Route::apiResource('v1/articles', ArticlesController::class);
//Route::apiResource('v1/roles', RolesController::class);
//Route::apiResource('v1/departments', DepartmentsController::class);
//Route::apiResource('v1/users', UsersController::class);

Route::post('v1/login',                  'App\Http\Controllers\Api\v1\UsersController@authenticate');

Route::get('v1/articles',                'App\Http\Controllers\Api\v1\ArticlesController@index');
Route::get('v1/categories',              'App\Http\Controllers\Api\v1\CategoriesController@index');
Route::get('v1/article/{article}',       'App\Http\Controllers\Api\v1\ArticlesController@show');
Route::get('v1/category/{category}',     'App\Http\Controllers\Api\v1\ArticlesController@show');

Route::get('v1/article/{article}/image', 'App\Http\Controllers\Api\v1\ArticlesController@image');

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::post('v1/user/register',     'App\Http\Controllers\Api\v1\UsersController@register');
    Route::get('v1/users',              'App\Http\Controllers\Api\v1\UsersController@index');
    Route::get('v1/user/{user}',        'App\Http\Controllers\Api\v1\UsersController@show');
    Route::put('v1/user/{user}',        'App\Http\Controllers\Api\v1\UsersController@update');
    Route::get('v1/user',               'App\Http\Controllers\Api\v1\UsersController@getAuthenticatedUser');
    
    Route::post('v1/articles',                  'App\Http\Controllers\Api\v1\ArticlesController@store');
    Route::post('v1/article/{article}',          'App\Http\Controllers\Api\v1\ArticlesController@update');
    //Route::put('v1/article/{article}',          'App\Http\Controllers\Api\v1\ArticlesController@update');
    Route::put('v1/article/{article}/approve',  'App\Http\Controllers\Api\v1\ArticlesController@approve');
    Route::delete('v1/article/{article}',       'App\Http\Controllers\Api\v1\ArticlesController@destroy');
    
    Route::post('v1/category',               'App\Http\Controllers\Api\v1\CategoriesController@store');
    Route::put('v1/category/{category}',     'App\Http\Controllers\Api\v1\CategoriesController@update');
    Route::delete('v1/category/{category}',  'App\Http\Controllers\Api\v1\CategoriesController@destroy');
});
