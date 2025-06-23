<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuth\RegisterController;
use App\Http\Controllers\AdminAuth\LoginController;
use App\Http\Controllers\AdminAuth\LogoutController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CoupenController;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\GroceryCategoryController;
use App\Http\Controllers\GroceryProductController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PincodeController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\TimeslotController;
use App\Http\Controllers\OrderBookController;
use App\Http\Controllers\UserController;

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
Route::get('/payment-test', function () {
    return view('razorpay_test');
});

Route::get('/', function () {
    return view('home');
});
Route::get('admin/register',[RegisterController::class,'showRegistrationForm']);
Route::post('/admin/register',[RegisterController::class, 'register'])->name('admin.register');
Route::get('admin/login',[LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login',[LoginController::class, 'login'])->name('login');

Route::middleware(['auth:admin', 'web', 'prevent-back-history'])->group(function(){

   // Route::get('/dashboard', function() {
     //   return view('dashboard');
   // });
    
    Route::get('/dashboard', function() {
        $today = \Carbon\Carbon::today();
        $orders = \App\Models\OrderBook::whereDate('created_at', $today)->get();
        $orderToday = $orders->count();
        $orderTotalAmt = $orders->sum('payment_amount');
        $totalCustomers = \App\Models\User::count();
        return view('dashboard', compact('orderToday', 'totalCustomers', 'orderTotalAmt'));
    });

    Route::get('/banner', [BannerController::class, 'index'])->name('banner.index');
    Route::post('/banner/post',[BannerController::class, 'store'])->name('banner.store');
    Route::get('/banner/{id}/edit',[BannerController::class, 'edit']);
    Route::put('/banner/update/{id}',[BannerController::class,'update'])->name('banner.update');
    Route::delete('/banner/delete/{id}',[BannerController::class, 'destroy'])->name('banner.destroy');

    Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
    Route::post('/category/post',[CategoryController::class, 'store'])->name('category.store');
    Route::get('/category/{id}/edit',[CategoryController::class, 'edit']);
    Route::put('/category/update/{id}',[CategoryController::class,'update'])->name('category.update');
    Route::delete('/category/delete/{id}',[CategoryController::class, 'destroy'])->name('category.destroy');

    
    Route::get('/food', [FoodController::class, 'index'])->name('food.index');
    Route::post('/food/post',[FoodController::class, 'store'])->name('food.store');
    Route::get('/food/{id}/edit',[FoodController::class, 'edit']);
    Route::put('/food/update/{id}',[FoodController::class,'update'])->name('food.update');
    Route::delete('/food/delete/{id}',[FoodController::class, 'destroy'])->name('food.destroy');

    Route::get('/recipe', [RecipeController::class, 'index'])->name('recipe.index');
    Route::post('/recipe/post',[RecipeController::class, 'store'])->name('recipe.store');
    Route::get('/recipe/{id}/edit',[RecipeController::class, 'edit']);
    Route::put('/recipe/update/{id}',[RecipeController::class,'update'])->name('recipe.update');
    Route::delete('/recipe/delete/{id}',[RecipeController::class, 'destroy'])->name('recipe.destroy');

    Route::get('/grocery/category', [GroceryCategoryController::class, 'index'])->name('grocery_category.index');
    Route::post('/grocery/category/post',[GroceryCategoryController::class, 'store'])->name('grocery_category.store');
    Route::get('/grocery/category/{id}/edit',[GroceryCategoryController::class, 'edit']);
    Route::put('/grocery/category/update/{id}',[GroceryCategoryController::class,'update'])->name('grocery_category.update');
    Route::delete('/grocery/category/delete/{id}',[GroceryCategoryController::class, 'destroy'])->name('grocery_category.destroy');

    Route::get('/grocery/product', [GroceryProductController::class, 'index'])->name('grocery_product.index');
    Route::post('/grocery/product/post',[GroceryProductController::class, 'store'])->name('grocery_product.store');
    Route::get('/grocery/product/{id}/edit',[GroceryProductController::class, 'edit']);
    Route::put('/grocery/product/update/{id}',[GroceryProductController::class,'update'])->name('grocery_product.update');
    Route::delete('/grocery/product/delete/{id}',[GroceryProductController::class, 'destroy'])->name('grocery_product.destroy');
    
    Route::get('/coupen', [CoupenController::class, 'index'])->name('coupen.index');
    Route::post('/coupen/post',[CoupenController::class, 'store'])->name('coupen.store');
    Route::get('/coupen/{id}/edit',[CoupenController::class, 'edit']);
    Route::put('/coupen/update/{id}',[CoupenController::class,'update'])->name('coupen.update');
    Route::delete('/coupen/delete/{id}',[CoupenController::class, 'destroy'])->name('coupen.destroy');

    Route::get('/timeslot', [TimeslotController::class, 'index'])->name('timeslot.index');
    Route::post('/timeslot/toggle-status/{id}', [TimeslotController::class, 'toggleStatus'])->name('timeslot.toggle');
    Route::post('/timeslot/post',[TimeslotController::class, 'store'])->name('timeslot.store');
    Route::get('/timeslot/{id}/edit',[TimeslotController::class, 'edit']);
    Route::put('/timeslot/update/{id}',[TimeslotController::class,'update'])->name('timeslot.update');
    Route::delete('/timeslot/delete/{id}',[TimeslotController::class, 'destroy'])->name('timeslot.destroy');

    Route::get('/notification', [NotificationController::class, 'index'])->name('notification.index');
    Route::post('/notification/post',[NotificationController::class, 'store'])->name('notification.store');
    Route::get('/notification/{id}/edit',[NotificationController::class, 'edit']);
    Route::put('/notification/update/{id}',[NotificationController::class,'update'])->name('notification.update');
    Route::delete('/notification/delete/{id}',[NotificationController::class, 'destroy'])->name('notification.destroy');

    Route::get('/pincode', [PincodeController::class, 'index'])->name('pincode.index');
    Route::post('/pincode/post',[PincodeController::class, 'store'])->name('pincode.store');
    Route::get('/pincode/{id}/edit',[PincodeController::class, 'edit']);
    Route::put('/pincode/update/{id}',[PincodeController::class,'update'])->name('pincode.update');
    Route::delete('/pincode/delete/{id}',[PincodeController::class, 'destroy'])->name('pincode.destroy');

    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/change-password', [UserController::class, 'changePassword']);
    Route::post('/change-password', [UserController::class, 'updatePassword'])->name('update.password');

    Route::get('/orderbook', [OrderBookController::class, 'index'])->name('orderbook.index');
    Route::get('/orderDetails/{orderBookId}', [OrderBookController::class, 'orderDetails']);
    Route::post('/changeOrderStatus/{OrderId}', [OrderBookController::class, 'updateOrderStatus']);
    Route::get('server-error',[ErrorController::class, 'serverError'])->name('error.server_error');

    Route::post('/admin/logout',[LogoutController::class, 'logout'])->name('admin.logout');
});

