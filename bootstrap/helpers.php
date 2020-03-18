<?php

use Illuminate\Support\Facades\Route;

function test_helper(){
    return 'ok';
}

//将请求的路由名，转换成css类名称
function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}