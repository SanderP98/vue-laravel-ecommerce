<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ShopController;

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
Route::get('/shop', [ShopController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::post('webhooks/mollie',[OrderController::class, 'webhook'])->name('webhooks.mollie');
Route::get('payment-methods/{bool}',[OrderController::class, 'getPaymentMethods']);
Route::get('payment-success/{is_singular}',[OrderController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/approvableReviews', [ProductController::class, 'showApprovableReviews']);
Route::get('/approvedReviews', [ProductController::class, 'showApprovedReviews']);
Route::get('/reviews', [ProductController::class, 'showAllReviews']);

Route::group(['middleware' => 'auth:api'], function(){
    Route::post('/upload-file', [ProductController::class, 'uploadFile']);
    Route::post('/add-review', [ProductController::class, 'addReview']);
    Route::post('molliepayment', [OrderController::class, 'preparePayment'])->name('molliepayment');
    Route::get('/users',[UserController::class, 'index']);
    Route::get('users/{user}',[UserController::class, 'show']);
    Route::patch('users/{user}',[UserController::class, 'update']);
    Route::get('users/{user}/orders',[UserController::class, 'showOrders']);
    Route::get('users/{user}/address',[UserController::class, 'showAddress']);
    Route::patch('products/{product}/units/add',[ProductController::class, 'updateUnits']);
    Route::patch('orders/{order}/deliver',[OrderController::class, 'deliverOrder']);
    Route::post('/addProductCategory', [ProductController::class, 'addProductCategory']);
    Route::patch('approveReview/{reviewId}', [ProductController::class, 'approveReview']);
    Route::delete('refuseReview/{reviewId}', [ProductController::class, 'refuseReview']);
    Route::delete('orders/{ids}/deleteMany', [OrderController::class, 'destroyMany']);
    Route::delete('products/{ids}/deleteMany', [ProductController::class, 'destroyMany']);
    Route::delete('products/{product}/delete', [ProductController::class, 'destroy']);
    Route::delete('orders/{order}/delete', [OrderController::class, 'destroy']);
    Route::resource('/orders', OrderController::class);

    Route::resource('/shop', ShopController::class)->except(['index']);
    Route::post('upload/shop-logo', [ShopController::class, 'uploadFile']);
    Route::resource('/address', AddressController::class)->except(['index','show']);
    Route::resource('/products', ProductController::class)->except(['index','show']);
});