<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/documentation', '\L5Swagger\Http\Controllers\SwaggerController@api');
