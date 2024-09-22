<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('admin');
});
Route::redirect('/laravel/login', '/login')->name('login');