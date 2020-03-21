<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', 'PagesController@root')->name('root');

Auth::routes(['verify' => true]);

Route::group(['middleware'=>['auth', 'verified']], function(){
    Route::resource('address', 'UserAddressController', ['except'=>'show']);

    Route::post('products/{product}/favorite', 'ProductsController@favor')->name('products.favor');
    Route::delete('products/{product}/favorite', 'ProductsController@disfavor')->name('products.disfavor');
});

Route::resource('products', 'ProductsController', ['only'=>['index', 'show']]);
