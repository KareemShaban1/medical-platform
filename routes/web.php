<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::get('/', function () {

    return view('welcome');
});


require __DIR__.'/admin.php';     
require __DIR__.'/clinic.php';
require __DIR__.'/supplier.php';