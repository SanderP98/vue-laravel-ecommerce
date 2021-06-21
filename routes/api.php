<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);
Route::get('/products', [ProductController::class, 'index']);
Route::post('/upload-file', [ProductController::class, 'uploadFile']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('payment-success',[OrderController::class, 'paymentSuccess'])->name('payment.success');
Route::post('webhooks/mollie',[OrderController::class, 'webhook'])->name('webhooks.mollie');
Route::resource('/orders', OrderController::class);

Route::group(['middleware' => 'auth:api'], function(){
    Route::post('molliepayment', [OrderController::class, 'preparePayment'])->name('mollie.payment');
    Route::get('/users',[UserController::class, 'index']);
    Route::get('users/{user}',[UserController::class, 'show']);
    Route::patch('users/{user}',[UserController::class, 'update']);
    Route::get('users/{user}/orders',[UserController::class, 'showOrders']);
    Route::patch('products/{product}/units/add',[ProductController::class, 'updateUnits']);
    Route::patch('orders/{order}/deliver',[OrderController::class, 'deliverOrder']);
    Route::delete('orders/{ids}/deleteMany', [OrderController::class, 'destroyMany']);
    Route::delete('products/{ids}/deleteMany', [ProductController::class, 'destroyMany']);
    Route::delete('products/{product}/delete', [ProductController::class, 'destroy']);
    Route::delete('orders/{order}/delete', [OrderController::class, 'destroy']);

    Route::resource('/products', ProductController::class)->except(['index','show']);
});