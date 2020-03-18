<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', 'PagesController@root')->name('root')->middleware(['verified']);

Auth::routes(['verify' => true]);

