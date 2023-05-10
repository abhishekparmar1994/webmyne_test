<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductsController;


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

Route::get('customers', [CustomerController::class, 'index'])->name('customers.index')->middleware('auth','admin');
Route::post('store-customer', [CustomerController::class, 'store'])->middleware('auth')->middleware('auth','admin');
Route::post('edit-customer', [CustomerController::class, 'edit'])->middleware('auth')->middleware('auth','admin');
Route::post('delete-customer', [CustomerController::class, 'destroy'])->middleware('auth')->middleware('auth','admin');

Route::get('admin-products-list', [ProductsController::class, 'index'])->name('products.index')->middleware('auth','admin');
Route::post('store-product', [ProductsController::class, 'store'])->middleware('auth','admin');
Route::post('edit-product', [ProductsController::class, 'edit'])->middleware('auth','admin');
Route::post('delete-product', [ProductsController::class, 'destroy'])->middleware('auth','admin');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
