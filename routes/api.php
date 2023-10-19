<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::post('join', [AuthController::class, 'join']);
//->middleware('throttle:50,1,join');
// confirm otp
// Route::post('check-user', [Auth\ResetPasswordController::class, 'checkUser']);
//->middleware('throttle:1,2,check-user');
Route::post('confirm', [AuthController::class, 'confirm']);

Route::post('login', [AuthController::class, 'login']);

Route::post('forgot-password', [AuthController::class, 'forgot_password']);
// Route::post('logout',  [AuthController::class, 'logout']);

Route::post('register', [AuthController::class, 'register'])
    ->middleware('throttle:20,1,register');






Route::get('trending-products',[ HomeController::class, 'trending_products']);
Route::get('best-seller-products',[ HomeController::class, 'best_sale']);
Route::get('top-sale',[ HomeController::class, 'top_rated']);
Route::get('may-like',[ HomeController::class, 'may_like']);
Route::get('product-list/{types}', [ProductController::class, 'index'])->name('product-list');
Route::get('product-details/{product}', [ProductController::class, 'show'])->name('product-details');

Route::get('category-parents',[CategoryController::class, 'index'])->name('category-parents');

// Route::get('product-list', [HomeController::class, 'product_list'])->name('product-list');

Route::put('profile', [AuthController::class, 'update_profile'])->name('update-profile');

Route::middleware('auth:sanctum')->group(function (){
    // return $request->user();

    Route::get('/profile', [AuthController::class, 'user']);
    Route::post('/logout',  [AuthController::class, 'logout']);


    Route::get('/orders/{orderId}', [EcommerceOrderController::class, 'getOrder']);
    Route::post('/orders', [EcommerceOrderController::class, 'createOrder']);
    Route::put('/orders/{orderId}/status', [EcommerceOrderController::class, 'updateOrderStatus']);
    Route::post('/orders/place-order', [OrderController::class, 'placeOrder']);
    // Add a product to the cart
    Route::post('/cart/add', [CartController::class, 'addToCart']);

    // Update the quantity of a product in the cart
    Route::put('/cart/{cartItemId}', [CartController::class, 'updateCartItem']);

    // Remove a product from the cart
    Route::delete('/cart/{cartItemId}', [CartController::class, 'removeFromCart']);

    // Get the contents of the user's cart
    Route::get('/cart', [CartController::class, 'getCart']);

    // Clear the entire cart
    Route::delete('/cart/clear', [CartController::class, 'clearCart']);



});
