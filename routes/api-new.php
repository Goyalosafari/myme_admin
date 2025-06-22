<?php
use App\Http\Controllers\Auth\AuthApiController;
use App\Http\Controllers\Auth\RegisterApiController;

use App\Http\Controllers\Api\BannerApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\CoupenApiController;
use App\Http\Controllers\Api\FavoritesApiController;
use App\Http\Controllers\Api\FoodApiController;
use App\Http\Controllers\Api\WalletApiController;
use App\Http\Controllers\Api\RatingsApiController;
use App\Http\Controllers\Api\RecipeApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\OrderBookApiController;
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


Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/forgot-password', [AuthApiController::class, 'forgot'])->name('password.reset');
Route::post('/logout', [AuthApiController::class, 'destroy']);
Route::post('/register', [RegisterApiController::class, 'store']);


Route::get('/categoryList/{type}', [CategoryApiController::class, 'index']);
Route::get('/bannerList', [BannerApiController::class, 'index']);

//special image link
Route::get('/bannerHome', [BannerApiController::class, 'Banner']);
Route::get('/homeCategory1', [BannerApiController::class, 'CategoryOne']);
Route::get('/homeCategory2', [BannerApiController::class, 'CategoryTwo']);
Route::get('/homeAd', [BannerApiController::class, 'AdOne']);
Route::get('/homeAd1', [BannerApiController::class, 'AdTwo']);

Route::get('/bannerList/{page_name}', [BannerApiController::class, 'bannerListByPage']);

Route::get('/coupenList', [CoupenApiController::class, 'index']);

Route::get('/foodList/{type}', [FoodApiController::class, 'index']);
Route::get('/foodListByCategory/{category_id}', [FoodApiController::class, 'foodByCategory']);
Route::get('/food/{food}', [FoodApiController::class, 'foodDetails']);
Route::get('/foodinOffer', [FoodApiController::class, 'foodInOffer']);

Route::get('/recipeList', [RecipeApiController::class, 'index']);
Route::get('/recipeList/{food_id}', [RecipeApiController::class, 'recipeByFood']);
Route::get('/recipe/{recipe}', [RecipeApiController::class, 'recipeDetails']);

Route::post('/addToCart', [OrderApiController::class, 'addToCart']);
    Route::get('/ordersByUserId', [OrderApiController::class, 'ordersByUserId']);
 Route::get('/cartByUserId', [OrderApiController::class, 'cartByUserId']);

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/profile', [UserApiController::class,'index']);
    Route::post('/update-profile', [UserApiController::class,'update']);
    
    Route::post('/addTofavorites', [FavoritesApiController::class, 'addTofavorites']);
    Route::get('/favoritesList', [FavoritesApiController::class, 'favoritesList']);
    Route::delete('/favorites/{favorite_id}', [FavoritesApiController::class, 'remove']);

    Route::post('/ratings', [RatingsApiController::class, 'rate']);

    Route::post('/wallet', [WalletApiController::class, 'store']);
    Route::get('/wallet', [WalletApiController::class, 'index']);

   // Route::post('/addToCart', [OrderApiController::class, 'addToCart']);
   // Route::get('/ordersByUserId', [OrderApiController::class, 'ordersByUserId']);
    Route::post('/createOrderBook', [OrderBookApiController::class, 'createOrderBook']);
    Route::post('/orderBookList', [OrderBookApiController::class, 'orderBookList']);
    Route::post('/cancelOrder', [OrderBookApiController::class, 'cancelOrder']);
    Route::put('/updateTimeslot/{orderId}', [OrderApiController::class, 'updateTimeSlot']);
});

