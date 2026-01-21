<?php

use Illuminate\Support\Facades\Route;

/* Route::view('/', 'welcome'); */

//to list products on welcome page
Route::get('/', function () {
    return view('welcome', [
        'products' => \App\Models\Product::all()
    ]);
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
