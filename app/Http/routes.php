<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/register', 'View\MemberController@toRegister');
Route::get('/login', 'View\MemberController@toLogin');
Route::get('/category', 'View\BookController@toCategory');
Route::get('/product/category_id/{category_id}', 'View\BookController@toProduct');
Route::get('/product/{product_id}', 'View\BookController@toPdtContent');

//路由分组---服务端接口组
Route::group(['prefix' => 'service'], function(){
    Route::post('register', 'Service\MemberController@register');
    Route::post('login', 'Service\MemberController@login');

    Route::get('validate_code/create', 'Service\ValidateController@create');
    Route::post('validate_phone/sendMsg', 'Service\ValidateController@sendMsg');
    Route::get('validate_email', 'Service\ValidateController@validateEmail');
    Route::get('category/parent_id/{parent_id}', 'Service\BookController@getCategoryByParentId');
    Route::get('cart/add/{product_id}', 'Service\CartController@addCart');
});

