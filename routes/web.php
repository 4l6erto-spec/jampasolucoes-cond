<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'dashboard')->name('home');
Route::get('/up', fn () => response()->json(['status' => 'ok']));
