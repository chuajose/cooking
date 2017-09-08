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

Route::post('login', 'Auth\LoginApiController@index');
Route::get('refreshToken', 'Auth\LoginApiController@refreshToken');

Route::group(
    ['middleware' => ['jwt.auth']], function () {

        
        Route::resource('ingredients', 'IngredientController');
        
    }
);
Route::resource('recipes', 'RecipeController');