<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\AuthApiController;
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

Route::get("login", "Api\ApiController@appLogin")->name('login');
Route::post("signUp", "Api\ApiController@signUp");
Route::post("login", "Api\ApiController@login");
Route::post("categoryList", "Api\ApiController@categoryList");
Route::post("forgotPassword", "Api\ApiController@forgotPassword");
Route::post("statusUpdate", "Api\ApiController@statusUpdate");
Route::post("emailSusbscriber", "Api\ApiController@emailSusbscriber");
Route::post('contactUs', 'Api\ApiController@contactUs');
Route::post("checkMobileExist", "Api\ApiController@checkMobileExist");


Route::post("customer_signUp", "Api\ApiController@customer_signUp");
Route::post("customer_login", "Api\ApiController@customer_login");

Route::post("tablet_signUp", "Api\ApiController@tablet_signUp");
Route::post("tablet_login", "Api\ApiController@tablet_login");

Route::post("itemList", "Api\ApiController@itemList");

// 
// Route::post("resendOtp", "Api\ApiController@resendOtp");
// Route::post("verifyOtp","Api\ApiController@verifyOtp")->name("verify-otp");
// Route::post("countryList", "Api\ApiController@countryList");
// Route::post("sendOtp", "Api\ApiController@sendOtp");
// Route::post("stateList", "Api\ApiController@stateList");
// Route::post("cityList", "Api\ApiController@cityList");
// Route::post("uploadImage", "Api\ApiController@uploadImage");
// Route::post("settingRule", "Api\ApiController@settingRule");
// Route::post("splashScreenList", "Api\ApiController@splashScreenList");


Route::middleware('auth:api')->group(function () {
    Route::post("logout", "Api\AuthApiController@logout");
    Route::post("homePageData", "Api\AuthApiController@homePageData");
    Route::post("addCustomer", "Api\AuthApiController@addCustomer");
    Route::post("customerList", "Api\AuthApiController@customerList");
    Route::post("notificationList", "Api\AuthApiController@notificationList");
    Route::post("readNotification", "Api\AuthApiController@readNotification");
    Route::post("deleteNotification", "Api\AuthApiController@deleteNotification");
    Route::post("updateProfile", "Api\AuthApiController@updateProfile");
    Route::post("changePassword", "Api\AuthApiController@changePassword");
    Route::post("deleteAccount", "Api\AuthApiController@deleteAccount");
    Route::post("profileDetail", "Api\AuthApiController@profileDetail");
    Route::post("postOfferRequest", "Api\AuthApiController@postOfferRequest");
    Route::post("addSalePerson", "Api\AuthApiController@addSalePerson");
    Route::post("salePersonList", "Api\AuthApiController@salePersonList");
    Route::post("updateSalePerson", "Api\AuthApiController@updateSalePerson");
    Route::post("addAttribute", "Api\AuthApiController@addAttribute");
    Route::post("attributeList", "Api\AuthApiController@attributeList");
    Route::post("updateAttribute", "Api\AuthApiController@updateAttribute");
    Route::post("addProduct", "Api\AuthApiController@addProduct");
    Route::post("productList", "Api\AuthApiController@productList");
    Route::post("updateProduct", "Api\AuthApiController@updateProduct");
    Route::post("createInvoice", "Api\AuthApiController@createInvoice");  
    Route::post("invoiceList", "Api\AuthApiController@invoiceList");
    Route::post("updateInvoice", "Api\AuthApiController@updateInvoice");
    Route::post("invoiceDetail", "Api\AuthApiController@invoiceDetail");
    Route::post("sendInvoice", "Api\AuthApiController@sendInvoice");
    Route::post("subscriptionList", "Api\AuthApiController@subscriptionList");
    Route::post("subscriptionUpgrade", "Api\AuthApiController@subscriptionUpgrade");
    Route::post("alreadyBuySubscriptionList", "Api\AuthApiController@alreadyBuySubscriptionList");
    Route::post("transactionList", "Api\AuthApiController@transactionList");
    Route::post("menuCategoryList", "Api\AuthApiController@menuCategoryList");
    Route::post("menuCategoryWiseProductList", "Api\AuthApiController@menuCategoryWiseProductList");
    Route::post("menuCategoryWiseItems", "Api\AuthApiController@menuCategoryWiseItems");
    Route::post("menuProductDetail", "Api\AuthApiController@menuProductDetail");
    Route::post("menuProductOrder", "Api\AuthApiController@menuProductOrder");

    Route::post("customer_profileDetail", "Api\AuthApiController@customer_profileDetail");
    Route::post("customer_offerList", "Api\AuthApiController@customer_offerList");
    Route::post("customer_megaOfferList", "Api\AuthApiController@customer_megaOfferList");
    Route::post("customer_recentlyAddedCouponList", "Api\AuthApiController@customer_recentlyAddedCouponList");
    Route::post("customer_recommendedOfferList", "Api\AuthApiController@customer_recommendedOfferList");
    Route::post("brandList", "Api\AuthApiController@brandList");

    Route::post("customerHomePageData", "Api\AuthApiController@customerHomePageData");


    
    // Route::post("transaction", "Api\AuthApiController@transaction");
    
    // Route::post("boxlist", "Api\AuthApiController@boxlist"); 
    // Route::post("boxbuy", "Api\AuthApiController@boxbuy");
    // Route::post("getMonthlyLeaderboard", "Api\AuthApiController@getMonthlyLeaderboard");

    Route::post("ReadNotification", "Api\AuthApiController@ReadNotification");
    Route::post("notificationDetail", "Api\AuthApiController@notificationDetail");
    
    Route::post("deleteAllNotifications", "Api\AuthApiController@deleteAllNotifications");
    Route::post("ReadAllNotification", "Api\AuthApiController@ReadAllNotification");
    
    Route::post("AddItem", "Api\AuthApiController@AddItem");

    Route::post("RestaurantItemAdd", "Api\AuthApiController@RestaurantItemAdd");
    Route::post("RestaurantItemIngredientAdd", "Api\AuthApiController@RestaurantItemIngredientAdd");
    Route::post("RestaurantOrderList", "Api\AuthApiController@RestaurantOrderList");
    Route::post("placeOrder", "Api\AuthApiController@placeOrder");
    Route::post("updateStatus", "Api\AuthApiController@updateStatus");
    
});

Route::post("cmspage/{slug?}", "Api\ApiController@cmspage");

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
