<?php
use App\Http\Controllers\Api\BannerApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\CoupenApiController;
use App\Http\Controllers\Api\FavoritesApiController;
use App\Http\Controllers\Api\FoodApiController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\OrderBookApiController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\PincodeApiController;
use App\Http\Controllers\Api\RatingsApiController;
use App\Http\Controllers\Api\RecipeApiController;
use App\Http\Controllers\Api\TimeslotApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Auth\AuthApiController;
use App\Http\Controllers\Auth\RegisterApiController;
use App\Http\Controllers\Api\UserEditApiController;
use App\Http\Controllers\Api\AddressApiController;
use App\Http\Controllers\Api\ConversionApiController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::post('/test-sms', [OrderBookApiController::class, 'smstest']);
Route::post('/order-sms', [OrderBookApiController::class, 'smsorder']);
Route::post('/cancel-sms', [OrderBookApiController::class, 'smscancel']);
///Route::post('/send-otp', [OrderBookApiController::class, 'sendOtp']);

Route::post('/deactivate-user', [UserController::class, 'deactivatewebUser']);

Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/forgot-password', [AuthApiController::class, 'forgot'])->name('password.reset');
Route::post('/forgot-password-otp', [AuthApiController::class, 'forgotPasswordOtp']);
Route::post('/logout', [AuthApiController::class, 'destroy']);
Route::post('/register', [RegisterApiController::class, 'store']);

///Route::post('/otp-sms', [RegisterApiController::class, 'smsOtp']);

Route::get('/categoryList/{type}', [CategoryApiController::class, 'index']);
Route::get('/bannerList', [BannerApiController::class, 'index']);

//special image link
Route::get('/bannerHome', [BannerApiController::class, 'Banner']);
Route::get('/homeCategory1', [BannerApiController::class, 'CategoryOne']);
Route::get('/homeCategory2', [BannerApiController::class, 'CategoryTwo']);
Route::get('/homeAd', [BannerApiController::class, 'AdOne']);
Route::get('/homeAd1', [BannerApiController::class, 'AdTwo']);

Route::get('/bannerFood1', [BannerApiController::class, 'AdOne']);
Route::get('/bannerFood2', [BannerApiController::class, 'AdTwo']);
Route::get('/bannerGrocery1', [BannerApiController::class, 'AdThree']);
Route::get('/bannerGrocery2', [BannerApiController::class, 'AdFour']);

Route::get('/offerfood1', [FoodApiController::class, 'Homefood1']);
Route::get('/offerfood2', [FoodApiController::class, 'Homefood2']);
Route::get('/offergrocery1', [FoodApiController::class, 'Homefood3']);
Route::get('/offergrocery2', [FoodApiController::class, 'Homefood4']);

Route::get('/coupenList', [CoupenApiController::class, 'index']);
Route::post('/coupenList', [CoupenApiController::class, 'filterCoupen']);

Route::get('/foodList/{type}', [FoodApiController::class, 'index']);
Route::get('/foodListByCategory/{category_id}', [FoodApiController::class, 'foodByCategory']);
Route::get('/foodListByCategoryId/{category_id}', [FoodApiController::class, 'foodByCategoryId']);

#Route::get('/foodListByCategory/{category_id}', [FoodApiController::class, 'foodByCategory']);   // for single category
#Route::get('/foodListByCategoryId/{category_id}', [FoodApiController::class, 'foodByCategoryId']); //for multiple categoryy

Route::get('/coupenCheck', [CoupenApiController::class, 'checkCoupon']);

Route::get('/food/{food}', [FoodApiController::class, 'foodDetails']);
Route::get('/timeslots', [TimeslotApiController::class, 'index']);
Route::post('/timeslotsDate', [TimeslotApiController::class, 'getTimeSlotByDate']);

Route::post('/cancelOrder', [OrderBookApiController::class, 'cancelOrder']);

Route::post('/orderBookList', [OrderBookApiController::class, 'orderBookList']);

Route::get('/recipeList', [RecipeApiController::class, 'index']);
Route::get('/recipeList/{food_id}', [RecipeApiController::class, 'recipeByFood']);
Route::get('/recipe/{recipe}', [RecipeApiController::class, 'recipeDetails']);

Route::post('/addToCart', [OrderApiController::class, 'addToCart']);

Route::post('/rate_food', [OrderApiController::class, 'AddFoodRating']);

Route::post('/updateOrder', [OrderApiController::class, 'updateOrder']);

Route::get('/ordersByUserId', [OrderApiController::class, 'ordersByUserId']);

Route::get('/usercartsum', [OrderApiController::class, 'userCartSum']);

Route::get('/ordersByTimeslot', [OrderApiController::class, 'ordersByTimeslot']); ///for timeslot testing

Route::get('/ordersByOrderId', [OrderApiController::class, 'ordersByOrderId']); ///order details new api

Route::get('/ordersListByUserId', [OrderApiController::class, 'ordersByListUserId']);
Route::get('/ordersGroupByUserId', [OrderApiController::class, 'ordersByGroupUserId']);

Route::get('/OrderHistory', [OrderBookApiController::class, 'ordersHistory']); //ordersHistoryDetails
Route::get('/OrdersHistoryDetails', [OrderApiController::class, 'ordersHistoryDetails']);

Route::get('/UserWallet', [OrderBookApiController::class, 'userWallet']); ///wallet sum

Route::post('/createOrderBook', [OrderBookApiController::class, 'createOrderBook']);

// Route::post('/test-sms', [OrderBookApiController::class, 'testSmsSending']); //smstest
Route::post('/check-user-by-mobile', [RegisterApiController::class, 'checkUserByMobile']);

Route::post('/cancelOrder', [OrderBookApiController::class, 'cancelOrder']);

Route::get('/cartSumByUserId', [OrderApiController::class, 'cartSumByUserId']);
Route::get('/gstSumByUserId', [OrderApiController::class, 'gstSumByUserId']);

Route::get('/Userprofile', [UserApiController::class, 'userInfo']);
Route::post('/update-profile', [UserApiController::class, 'update']);
Route::post('/send-otp', [RegisterApiController::class, 'sendOtp']);
Route::post('/send-login-otp', [RegisterApiController::class, 'sendLoginOtp']);

Route::delete('/users/address', [UserApiController::class, 'deleteAddress']);

Route::get('/pincode', [PincodeApiController::class, 'index']);
Route::post('/pincodeValidation', [PincodeApiController::class, 'pincodeValidation']);

Route::get('/notifications', [NotificationApiController::class, 'index']);
Route::get('/filterNotification', [NotificationApiController::class, 'filterNotification']);
Route::post('/filterNotificationByOrder', [NotificationApiController::class, 'filterNotificationByOrder']);
Route::post('/ratings', [RatingsApiController::class, 'rate']);

Route::post('/delete-user', [UserApiController::class, 'deactivateUser']);

Route::post('/pass-user', [UserApiController::class, 'newPassword']);

// Route::post('/createOrderBook', [OrderBookApiController::class, 'createOrderBook']);
//Route::get('/ordersByUserId', [OrderApiController::class, 'ordersByUserId']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [UserApiController::class, 'index']);
    // Route::post('/update-profile', [UserApiController::class,'update']);

    Route::post('/addTofavorites', [FavoritesApiController::class, 'addTofavorites']);
    Route::get('/favoritesList', [FavoritesApiController::class, 'favoritesList']);
    Route::delete('/favorites/{favorite_id}', [FavoritesApiController::class, 'remove']);

    // Route::post('/addToCart', [OrderApiController::class, 'addToCart']);
    //Route::post('/createOrderBook', [OrderBookApiController::class, 'createOrderBook']);
    Route::post('/orderBookList', [OrderBookApiController::class, 'orderBookList']);
    // Route::get('/ordersByUserId', [OrderApiController::class, 'ordersByUserId']);
    // Route::get('/ordersListByUserId', [OrderApiController::class, 'ordersByListUserId']);
    Route::put('/updateTimeslot/{orderId}', [OrderApiController::class, 'updateTimeSlot']);
});
// User profile edit APIs
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user/update-name', [UserEditApiController::class, 'updateName']);
    Route::post('/user/update-email', [UserEditApiController::class, 'updateEmail']);
    Route::post('/user/update-mobile', [UserEditApiController::class, 'updateMobile']);
});
Route::post('/user/update-password', [UserEditApiController::class, 'updatePassword']);
Route::post('/user/send-email-otp', [UserEditApiController::class, 'sendEmailOtp']);
Route::get('razorpay-payment', [PaymentApiController::class, 'index'])->name('payment');
Route::post('razorpay-payment', [PaymentApiController::class, 'store'])->name('razorpay.payment.store');
Route::get('/has-rated-food', [OrderApiController::class, 'hasRatedFood']);
Route::post('/redeem-coins', [OrderBookApiController::class, 'redeemCoins']);
Route::get('/time-slots', [TimeSlotApiController::class, 'index']);
Route::middleware('auth:sanctum')->post('/user/add-address', [AddressApiController::class, 'store']);
Route::middleware('auth:sanctum')->put('/user/address', [App\Http\Controllers\Api\AddressApiController::class, 'updateByBody']);
Route::middleware('auth:sanctum')->get('/active-conversions', [ConversionApiController::class, 'activeCollections']);
