<?php

use App\Http\Controllers\Auth\ProductController;
use App\Http\Controllers\Auth\ProductListController;
use App\Http\Controllers\Auth\CartController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/dbConnect', function () {
    return view('dbConnect');
});


// Products List

Route::get('/products', [ProductListController::class, 'create']);

// Add Products

Route::get('/products/new', [ProductController::class, 'create'])->middleware('auth');

Route::post('/products/new', [ProductController::class, 'store'])->middleware('auth');

// Product Detail Page

Route::get('/products/{product}', function () {
    return view('auth.productDetailPage');
});

Route::post('/products/{product}', function () {
    return view('auth.productDetailPage');
});

// Shopping Cart

Route::get('/cart', [CartController::class, 'create'])->middleware('auth');


