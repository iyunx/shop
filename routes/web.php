<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', 'PagesController@root')->name('root');

Auth::routes(['verify' => true]);

Route::group(['middleware'=>['auth', 'verified']],function(){
    Route::get('address', 'UserAddressController@index')->name('address.index');
    Route::resource('address', 'UserAddressController', ['except'=>'show']);
});

Route::resource('products', 'ProductsController');
