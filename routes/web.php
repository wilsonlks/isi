<?php

use App\Http\Controllers\Auth\ProductController;
use App\Http\Controllers\Auth\ProductListController;
use App\Http\Controllers\Auth\CartController;
use App\Http\Controllers\Auth\OrderController;
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


// Products List and Product Catalog

Route::get('/products', [ProductListController::class, 'get_product_list']);

// Add Products

Route::get('/products/new', [ProductController::class, 'create'])->middleware('auth');

Route::post('/products/new', [ProductController::class, 'store'])->middleware('auth');

// Product Detail Page

Route::get('/products/{product}', function () {
    return view('auth.productDetailPage');
});

Route::post('/products/{product}', function () {
    return view('auth.productDetailPage');
})->middleware('auth');

// Edit Products

Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->middleware('auth');

Route::post('/products/{product}/edit', [ProductController::class, 'edit'])->middleware('auth');

// Shopping Cart

Route::get('/cart', [CartController::class, 'get_shopping_cart'])->middleware('auth');

Route::post('/cart', [CartController::class, 'purchase'])->middleware('auth');

// Purchase Tracking Page and Purchase Order List Page

Route::get('/orders', [OrderController::class, 'get_orders'])->middleware('auth');

Route::post('/orders', [OrderController::class, 'get_orders'])->middleware('auth');

// Purchase Order Detail Page and Purchase Order Processing Page

Route::get('/orders/{order}', [OrderController::class, 'get_order_detail'])->middleware('auth');

Route::post('/orders/{order}', [OrderController::class, 'process_order_detail'])->middleware('auth');

// Rate and Review Page

Route::get('/orders/{order}/products/{product}/reviews', [OrderController::class, 'rate_and_review'])->middleware('auth');

Route::post('/orders/{order}/products/{product}/reviews', [OrderController::class, 'rate_and_review'])->middleware('auth');

// Best Selling Products

Route::get('/bestSelling', [ProductController::class, 'best_selling'])->middleware('auth');
