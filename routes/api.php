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
// user routes
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'auth', 'middleware' => 'cors'], function() {
    Route::post('login', 'UserController@login');
    Route::post('posts/files', 'PostController@files');
    Route::post('posts/image', 'PostController@post_image');
    Route::post('users/image', 'UserController@user_img');
    Route::post('forgotPassword', 'PasswordResetController@create');
    Route::post('reset', 'PasswordResetController@reset');
});

Route::group(['middleware' => ['auth:api', 'cors'], 'prefix' => 'auth'], function() {
    Route::get('logout', 'UserController@logout');
    Route::get('user', 'UserController@user_details');
    Route::get('show_user/{user}', 'UserController@show');
    Route::get('users', 'UserController@index');
    Route::put('update_user', 'UserController@update');
    Route::delete('delete/{user}', 'UserController@destroy');
    Route::put('make_admin/{user}', 'UserController@makeAdmin');
    Route::post('register', 'UserController@register');
    Route::put('updatePassword', 'UserController@updatePassword');
});

//posts routes

Route::group(['middleware' => ['auth:api', 'cors'], 'prefix' => 'auth'], function() {
    Route::get('main', 'MainController@scrape');
    Route::apiResource('posts', 'PostController');
    Route::put('posts/{post}/publish', 'PostController@publish');
    Route::put('posts/{post}/unpublish', 'PostController@unpublish');
    Route::apiResource('categories', 'CategoryController');
    Route::apiResource('tags', 'TagController');
});

Route::group(['prefix' => 'archive', 'middleware' => 'cors'], function() {
    Route::get('posts', 'FrontEndController@posts');
    Route::get('posts/{post}', 'FrontEndController@show_post');
    Route::get('categories', 'FrontEndController@categories');
    Route::get('categories/{category}', 'FrontEndController@category_posts');
    Route::get('tags', 'FrontEndController@tags');
    Route::get('tags/{tag}', 'FrontEndController@tag_posts');
    Route::get('home', 'FrontEndController@home_posts');
    Route::get('storeFiles','FrontEndController@storeFiles');
    Route::get('search', 'FrontEndController@search');
});