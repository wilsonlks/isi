<?php

use App\Http\Controllers\Auth\ProductController;
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

Route::get('/products', function () {
    return view('auth.productList');
});

Route::get('/dbConnect', function () {
    return view('dbConnect');
});

Route::get('/products/new', [ProductController::class, 'create']);

Route::post('/products/new', function () {
    return view('auth.addProduct');
});

Route::get('/products/{product}', function () {
    return view('auth.productDetailPage');
});

// Route::get('/addProduct', function () {
//     return view('auth.addProduct');
// });

