<?php

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
Route::get('clear-cache', function() {
    $exitCode = Artisan::call('config:clear');
    // return what you want
});
/*Route::get('/', function () {
    return view('welcome');
});*/

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('download/{filename}', 'HomeController@downloadFile')->name('downloadFile');
Route::get('/', 'HomeController@welcome')->name('welcome');

Route::group(['middleware' => ['auth']], function() {
	Route::group(['prefix'=>'admin','middleware' => ['auth','admin']], function() {
    	// Index and dashboard 
		Route::get('/', ['uses' => 'Backend\HomeController@index'])->name('dashboard');
		Route::get('income', ['uses' => 'Backend\HomeController@incomeChart'])->name('Income');
		Route::get('change-password/edit', ['uses' => 'Backend\HomeController@changePassword'])->name('changepassword');
		Route::post('change-password', ['uses' => 'Backend\HomeController@changePassword'])->name('changepasswordPost');

		//admins Module
		Route::get('admins/list', ['uses' => 'Backend\AdminController@index'])->name('admins');
		Route::post('adminsAjax', ['uses' => 'Backend\AdminController@adminsAjax'])->name('adminsAjax');
		Route::get('admins/add', ['uses' => 'Backend\AdminController@create'])->name('createAdmins');
		Route::post('admins/store', ['uses' => 'Backend\AdminController@store'])->name('addAdmins');
		Route::get('admins/edit/{id?}', ['uses' => 'Backend\AdminController@edit'])->name('editAdmins');
		Route::post('admins/update/{id?}', ['uses' => 'Backend\AdminController@update'])->name('updateAdmins');
		Route::get('admins/view/{id?}', ['uses' => 'Backend\AdminController@show'])->name('viewAdmins');
		Route::get('admins/checkAdmins/{id?}', ['uses' => 'Backend\AdminController@checkAdmins'])->name('checkAdmins');
		Route::post('admins/changeStatus', ['uses' => 'Backend\AdminController@updateStatus'])->name('changeStatusAdmins');
		Route::post('admins/changeStatusAjax', ['uses' => 'Backend\AdminController@updateStatusAjax'])->name('changeStatusAjaxAdmins');
		Route::post('/admins/delete', ['uses' => 'Backend\AdminController@destroy'])->name('deleteAdmins');
		Route::post('/admins/bulkdelete', ['uses' => 'Backend\AdminController@bulkdelete'])->name('deleteAdminsBulk');
		Route::post('/admins/bulkupdate_status', ['uses' => 'Backend\AdminController@bulkchangeStatus'])->name('changeStatusAdminsBulk');

		//CMS Module
		Route::get('/cms/list', ['uses' => 'Backend\CMSController@index'])->name('cms');
		Route::get('/cmsAjax', ['uses' => 'Backend\CMSController@cmsAjax'])->name('cmsAjax');
		Route::get('/cms/view/{id?}', ['uses' => 'Backend\CMSController@show'])->name('viewCMS');
		Route::get('/cms/edit/{id?}', ['uses' => 'Backend\CMSController@edit'])->name('editCMS');
		Route::post('/cms/update/{id?}', ['uses' => 'Backend\CMSController@update'])->name('updateCMS');
		Route::post('/cms/deleteFAQ/{id?}', ['uses' => 'Backend\CMSController@destroy'])->name('deleteFAQ');

		//Email Template Module
		Route::get('/emailtemplate/list', ['uses' => 'Backend\EmailTemplateController@index'])->name('emailtemplate');
		Route::post('/emailtemplateAjax', ['uses' => 'Backend\EmailTemplateController@emailtemplateAjax'])->name('emailtemplateAjax');
		Route::get('/emailtemplate/view/{id?}', ['uses' => 'Backend\EmailTemplateController@show'])->name('vieweditemailtemplate');
		Route::get('/emailtemplate/edit/{id?}', ['uses' => 'Backend\EmailTemplateController@edit'])->name('editemailtemplate');
		Route::post('/emailtemplate/update/{id?}', ['uses' => 'Backend\EmailTemplateController@update'])->name('updateemailtemplate');
		Route::post('/emailtemplate/bulkupdate_status', ['uses' => 'Backend\EmailTemplateController@bulkchangeStatus'])->name('changeStatusEmailtemplateBulk');
		Route::post('emailtemplate/changeStatusAjax', ['uses' => 'Backend\EmailTemplateController@updateStatusAjax'])
		->name('changeStatusEmailtemplate');
		
		//countries Module
		Route::get('/countries/list', ['uses' => 'Backend\CountryController@index'])->name('countries');
		Route::get('/countries/add', ['uses' => 'Backend\CountryController@create'])->name('createCountries');

		Route::post('/countriesAjax', ['uses' => 'Backend\CountryController@countryAjax'])->name('countriesAjax');
		Route::get('/countries/view/{id?}', ['uses' => 'Backend\CountryController@show'])->name('viewCountries');
		Route::get('/countries/edit/{id?}', ['uses' => 'Backend\CountryController@edit'])->name('editCountries');
		Route::post('/countries/store', ['uses' => 'Backend\CountryController@store'])->name('storeCountries');
		Route::post('/countries/update/{id?}', ['uses' => 'Backend\CountryController@update'])->name('updateCountries');
		Route::post('/countries/deleteCountries/{id?}', ['uses' => 'Backend\CountryController@destroy'])->name('deleteCountries');
		Route::get('/countries/checkCountryName/{id?}', ['uses' => 'Backend\CountryController@checkCountryName'])->name('checkCountryName');

		Route::post('/countries/bulkdelete', ['uses' => 'Backend\CountryController@bulkdelete'])->name('deleteCountriesBulk');
		Route::post('/countries/bulkupdate_status', ['uses' => 'Backend\CountryController@bulkchangeStatus'])->name('changeStatusCountriessBulk');
		Route::post('countries/changeStatusAjax', ['uses' => 'Backend\CountryController@updateStatusAjax'])->name('changeStatusAjaxCountries');
		Route::post('countries/delete', ['uses' => 'Backend\CountryController@destroy'])->name('deleteCounties');


		//categories Module
		Route::get('/categories/list', ['uses' => 'Backend\CategoryController@index'])->name('categories');
		Route::get('/categories/add', ['uses' => 'Backend\CategoryController@create'])->name('createCategories');
		Route::post('/categoriesAjax', ['uses' => 'Backend\CategoryController@categoriesAjax'])->name('categoriesAjax');
		Route::get('/categories/view/{id?}', ['uses' => 'Backend\CategoryController@show'])->name('viewCategories');
		Route::get('/categories/edit/{id?}', ['uses' => 'Backend\CategoryController@edit'])->name('editCategories');
		Route::post('/categories/store', ['uses' => 'Backend\CategoryController@store'])->name('storeCategories');
		Route::post('/categories/update/{id?}', ['uses' => 'Backend\CategoryController@update'])->name('updateCategories');
		Route::post('/categories/deleteCategories/{id?}', ['uses' => 'Backend\CategoryController@destroy'])->name('deleteCategories');
		Route::get('/categories/checkCategoryName/{id?}', ['uses' => 'Backend\CategoryController@checkCategoryName'])->name('checkCategoryName');
		Route::post('/categories/bulkdelete', ['uses' => 'Backend\CategoryController@bulkdelete'])->name('deleteCategoriesBulk');
		Route::post('/categories/bulkupdate_status', ['uses' => 'Backend\CategoryController@bulkchangeStatus'])->name('changeStatusCategoryBulk');
		Route::post('categories/changeStatusAjax', ['uses' => 'Backend\CategoryController@updateStatusAjax'])->name('changeStatusAjaxCategories');
		Route::post('categories/delete', ['uses' => 'Backend\CategoryController@destroy'])->name('deletecategories');


		//banner Manager
		Route::get('/banners/list', ['uses' => 'Backend\BannerController@index'])->name('banners');
		Route::post('/banners', ['uses' => 'Backend\BannerController@bannerAjax'])->name('bannerAjax');
		Route::get('/banners/add', ['uses' => 'Backend\BannerController@create'])->name('createBanner');
		Route::get('/banners/checkBanner/{id?}', ['uses' => 'Backend\BannerController@checkBanner'])->name('checkBanner');
		Route::post('/banners/store', ['uses' => 'Backend\BannerController@store'])->name('addBanner');
		Route::get('/banners/view/{id?}', ['uses' => 'Backend\BannerController@show'])->name('viewBanner');
		Route::get('/banners/edit/{id?}', ['uses' => 'Backend\BannerController@edit'])->name('editBanner');
		Route::post('/banners/update/{id?}', ['uses' => 'Backend\BannerController@update'])->name('updateBanner');
		Route::post('/banners/update_status', ['uses' => 'Backend\BannerController@updateStatus'])->name('changeStatusBanner');
		Route::post('/banners/update_statusAjax', ['uses' => 'Backend\BannerController@updateStatusAjax'])->name('changeStatusAjaxBanner');
		Route::post('/banners/delete', ['uses' => 'Backend\BannerController@destroy'])->name('deleteBanner');
		Route::post('/banners/bulkdelete', ['uses' => 'Backend\BannerController@bulkdelete'])->name('deleteBanners');
		Route::post('/banners/bulkupdate_status', ['uses' => 'Backend\BannerController@bulkchangeStatus'])->name('changeStatusBanners');

		//subscription Manager

		Route::get('/subscriptions/list', ['uses' => 'Backend\SubscriptionController@index'])->name('subscriptions');
		Route::post('subscriptionsAjax', ['uses' => 'Backend\SubscriptionController@subscriptionsAjax'])->name('SubscriptionsAjax');
		Route::get('subscriptions/add', ['uses' => 'Backend\SubscriptionController@create'])->name('createSubscriptions');
		Route::post('subscriptions/store', ['uses' => 'Backend\SubscriptionController@store'])->name('addSubscriptions');
		Route::get('subscriptions/edit/{id?}', ['uses' => 'Backend\SubscriptionController@edit'])->name('editSubscriptions');
		Route::post('subscriptions/update/{id?}', ['uses' => 'Backend\SubscriptionController@update'])->name('updateSubscriptions');
		Route::get('subscriptions/view/{id?}', ['uses' => 'Backend\SubscriptionController@show'])->name('viewSubscriptions');
		Route::get('subscriptions/checkUsers/{id?}', ['uses' => 'Backend\SubscriptionController@checkSubscriptions'])->name('checkSubscriptions');
		Route::post('subscriptions/changeStatus', ['uses' => 'Backend\SubscriptionController@updateStatus'])->name('changeStatusSubscriptions');
		Route::post('subscriptions/changeStatusAjax', ['uses' => 'Backend\SubscriptionController@updateStatusAjax'])->name('changeStatusAjaxSubscriptions');
		Route::post('/subscriptions/delete', ['uses' => 'Backend\SubscriptionController@destroy'])->name('deleteSubscriptions');
		Route::post('/subscriptions/bulkdelete', ['uses' => 'Backend\SubscriptionController@bulkdelete'])->name('deleteSubscriptionsBulk');
		Route::post('/subscriptions/bulkupdate_status', ['uses' => 'Backend\SubscriptionController@bulkchangeStatus'])->name('changeStatusSubscriptionsBulk');

		
		
		//TransactionManagment

		Route::get('/transactions/list', ['uses' => 'Backend\TransactionController@index'])->name('transactions');
		Route::post('/transactions', ['uses' => 'Backend\TransactionController@transactionAjax'])->name('transactionAjax');
		Route::get('/transactions/add', ['uses' => 'Backend\TransactionController@create'])->name('createTransaction');
		Route::get('/transactions/checkTransaction/{id?}', ['uses' => 'Backend\TransactionController@checkTransaction'])->name('checkTransaction');
		Route::post('/transactions/store', ['uses' => 'Backend\TransactionController@store'])->name('addTransaction');
		Route::get('/transactions/view/{id?}', ['uses' => 'Backend\TransactionController@show'])->name('viewTransaction');
		Route::get('/transactions/edit/{id?}', ['uses' => 'Backend\TransactionController@edit'])->name('editTransaction');
		Route::post('/transactions/update/{id?}', ['uses' => 'Backend\TransactionController@update'])->name('updateTransaction');
		Route::post('/transactions/update_status', ['uses' => 'Backend\TransactionController@updateStatus'])->name('changeStatusTransaction');
		Route::post('/transactions/update_statusAjax', ['uses' => 'Backend\TransactionController@updateStatusAjax'])->name('changeStatusAjaxTransaction');
		Route::post('/transactions/delete', ['uses' => 'Backend\TransactionController@destroy'])->name('deleteTransaction');
		Route::post('/transactions/bulkdelete', ['uses' => 'Backend\TransactionController@bulkdelete'])->name('deleteTransaction');
		Route::post('/transactions/bulkupdate_status', ['uses' => 'Backend\TransactionController@bulkchangeStatus'])->name('changeStatusTransactions');
		


		//state Module
		Route::get('/states/list', ['uses' => 'Backend\StateController@index'])->name('states');
		Route::get('/states/add', ['uses' => 'Backend\StateController@create'])->name('createStates');

		Route::post('/statesAjax', ['uses' => 'Backend\StateController@statesAjax'])->name('statesAjax');
		Route::get('/states/view/{id?}', ['uses' => 'Backend\StateController@show'])->name('viewStates');
		Route::get('/states/edit/{id?}', ['uses' => 'Backend\StateController@edit'])->name('editStates');
		Route::post('/states/store', ['uses' => 'Backend\StateController@store'])->name('storeStates');
		Route::post('/states/update/{id?}', ['uses' => 'Backend\StateController@update'])->name('updateStates');
		Route::post('/states/deleteStates/{id?}', ['uses' => 'Backend\StateController@destroy'])->name('deleteStates');
		Route::get('/states/checkStatesName/{id?}', ['uses' => 'Backend\StateController@checkStatesName'])->name('checkStatesName');

		Route::post('/states/bulkdelete', ['uses' => 'Backend\StateController@bulkdelete'])->name('deleteStatesBulk');
		Route::post('/states/bulkupdate_status', ['uses' => 'Backend\StateController@bulkchangeStatus'])->name('changeStatusStatesBulk');
		Route::post('states/changeStatusAjax', ['uses' => 'Backend\StateController@updateStatusAjax'])->name('changeStatusAjaxStates');
		Route::post('states/delete', ['uses' => 'Backend\StateController@destroy'])->name('deleteStates');

		//City Module
		Route::get('/cities/list', ['uses' => 'Backend\CityController@index'])->name('cities');
		Route::get('/cities/add', ['uses' => 'Backend\CityController@create'])->name('createCities');
		Route::post('/citiesAjax', ['uses' => 'Backend\CityController@citiesAjax'])->name('citiesAjax');
		Route::get('/cities/view/{id?}', ['uses' => 'Backend\CityController@show'])->name('viewCities');
		Route::get('/cities/edit/{id?}', ['uses' => 'Backend\CityController@edit'])->name('editCities');
		Route::post('/cities/store', ['uses' => 'Backend\CityController@store'])->name('storeCities');
		Route::post('/cities/update/{id?}', ['uses' => 'Backend\CityController@update'])->name('updateCities');
		Route::post('/cities/deleteStates/{id?}', ['uses' => 'Backend\CityController@destroy'])->name('deleteCities');
		Route::get('/cities/checkCitiesName/{id?}', ['uses' => 'Backend\CityController@checkCitiesName'])->name('checkCitiesName');
		Route::post('/cities/bulkdelete', ['uses' => 'Backend\CityController@bulkdelete'])->name('deleteCitiesBulk');
		Route::post('/cities/bulkupdate_status', ['uses' => 'Backend\CityController@bulkchangeStatus'])->name('changeStatusCitiesBulk');
		Route::post('cities/changeStatusAjax', ['uses' => 'Backend\CityController@updateStatusAjax'])->name('changeStatusAjaxCities');
		Route::post('cities/delete', ['uses' => 'Backend\CityController@destroy'])->name('deleteCities');


		//Regions Module
		Route::get('/regions/list', ['uses' => 'Backend\RegionController@index'])->name('regions');
		Route::get('/regions/add', ['uses' => 'Backend\RegionController@create'])->name('createRegions');
		Route::post('/regionsAjax', ['uses' => 'Backend\RegionController@regionsAjax'])->name('regionsAjax');
		Route::get('/regions/view/{id?}', ['uses' => 'Backend\RegionController@show'])->name('viewRegions');
		Route::get('/regions/edit/{id?}', ['uses' => 'Backend\RegionController@edit'])->name('editRegions');
		Route::post('/regions/store', ['uses' => 'Backend\RegionController@store'])->name('storeRegions');
		Route::post('/regions/update/{id?}', ['uses' => 'Backend\RegionController@update'])->name('updateRegions');
		Route::post('/regions/deleteRegions/{id?}', ['uses' => 'Backend\RegionController@destroy'])->name('deleteRegions');
		Route::get('/regions/checkRegionsName/{id?}', ['uses' => 'Backend\RegionController@checkRegionsName'])->name('checkRegionsName');
		Route::post('/regions/bulkdelete', ['uses' => 'Backend\RegionController@bulkdelete'])->name('deleteRegionsBulk');
		Route::post('/regions/bulkupdate_status', ['uses' => 'Backend\RegionController@bulkchangeStatus'])->name('changeStatusRegionsBulk');
		Route::post('regions/changeStatusAjax', ['uses' => 'Backend\RegionController@updateStatusAjax'])->name('changeStatusAjaxRegions');
		Route::post('regions/delete', ['uses' => 'Backend\RegionController@destroy'])->name('deleteRegions');


		//users Module
		Route::get('users/list', ['uses' => 'UserController@index'])->name('users');
		Route::post('usersAjax', ['uses' => 'UserController@usersAjax'])->name('usersAjax');
		Route::get('users/add', ['uses' => 'UserController@create'])->name('createUsers');
		Route::post('users/store', ['uses' => 'UserController@store'])->name('addUsers');
		Route::get('users/edit/{id?}', ['uses' => 'UserController@edit'])->name('editUsers');
		Route::post('users/update/{id?}', ['uses' => 'UserController@update'])->name('updateUsers');
		Route::get('users/view/{id?}', ['uses' => 'UserController@show'])->name('viewUsers');
		Route::get('users/checkUsers/{id?}', ['uses' => 'UserController@checkUsers'])->name('checkUsers');
		Route::post('users/changeStatus', ['uses' => 'UserController@updateStatus'])->name('changeStatusUsers');
		Route::post('users/changeStatusAjax', ['uses' => 'UserController@updateStatusAjax'])->name('changeStatusAjaxUsers');
		Route::post('/users/delete', ['uses' => 'UserController@destroy'])->name('deleteUsers');
		Route::post('/users/bulkdelete', ['uses' => 'UserController@bulkdelete'])->name('deleteUsersBulk');
		Route::post('/users/bulkupdate_status', ['uses' => 'UserController@bulkchangeStatus'])->name('changeStatusUsersBulk');




		//Role Module
		Route::get('/roles/list', ['uses' => 'RoleController@index'])->name('roles');
		Route::post('/roleAjax', ['uses' => 'RoleController@roleAjax'])->name('roleAjax');
		Route::get('/roles/add', ['uses' => 'RoleController@create'])->name('createRole');
		Route::get('/roles/checkRole/{id?}', ['uses' => 'RoleController@checkRole'])->name('checkRole');
		Route::post('/roles/store', ['uses' => 'RoleController@store'])->name('addRole');
		Route::get('/roles/view/{id?}', ['uses' => 'RoleController@show'])->name('viewRole');
		Route::get('/roles/edit/{id?}', ['uses' => 'RoleController@edit'])->name('editRole');
		Route::post('/roles/update/{id?}', ['uses' => 'RoleController@update'])->name('updateRole');
		Route::post('/roles/update_status', ['uses' => 'RoleController@updateStatus'])->name('changeStatusRole');
		Route::post('/roles/update_statusAjax', ['uses' => 'RoleController@updateStatusAjax'])->name('changeStatusAjaxRole');
		Route::post('/roles/delete', ['uses' => 'RoleController@destroy'])->name('deleteRole');
		Route::post('/roles/bulkdelete', ['uses' => 'RoleController@bulkdelete'])->name('deleteRoles');
		Route::post('/roles/bulkupdate_status', ['uses' => 'RoleController@bulkchangeStatus'])->name('changeStatusRoles');

		//pushs Module  
		Route::get('pushs/list', ['uses' => 'Backend\PushController@index'])->name('pushs');
		Route::post('pushsAjax', ['uses' => 'Backend\PushController@pushsAjax'])->name('pushsAjax');
		Route::post('pushs/store', ['uses' => 'Backend\PushController@store'])->name('addPushs');
		Route::get('pushs/view/{id?}', ['uses' => 'Backend\PushController@show'])->name('viewPushs');



        //SplashScreen Module
		Route::get('/splashscreens/list', ['uses' => 'Backend\SplashScreenController@index'])->name('splashscreens');
		Route::post('/splashscreenAjax', ['uses' => 'Backend\SplashScreenController@splashscreenAjax'])->name('splashscreenAjax');
		Route::get('/splashscreens/add', ['uses' => 'Backend\SplashScreenController@create'])->name('createSplashScreen');
		Route::get('/splashscreens/checkSplashScreen/{id?}', ['uses' => 'Backend\SplashScreenController@checkSplashScreen'])->name('checkSplashScreen');
		Route::post('/splashscreens/store', ['uses' => 'Backend\SplashScreenController@store'])->name('addSplashScreen');
		Route::get('/splashscreens/view/{id?}', ['uses' => 'Backend\SplashScreenController@show'])->name('viewSplashScreen');
		Route::get('/splashscreens/edit/{id?}', ['uses' => 'Backend\SplashScreenController@edit'])->name('editSplashScreen');
		Route::post('/splashscreens/update/{id?}', ['uses' => 'Backend\SplashScreenController@update'])->name('updateSplashScreen');
		Route::post('/splashscreens/update_status', ['uses' => 'Backend\SplashScreenController@updateStatus'])->name('changeStatusSplashScreen');
		Route::post('/splashscreens/update_statusAjax', ['uses' => 'Backend\SplashScreenController@updateStatusAjax'])->name('changeStatusAjaxSplashScreen');
		Route::post('/splashscreens/delete', ['uses' => 'Backend\SplashScreenController@destroy'])->name('deleteSplashScreen');
		Route::post('/splashscreens/bulkdelete', ['uses' => 'Backend\SplashScreenController@bulkdelete'])->name('deleteSplashScreens');
		Route::post('/splashscreens/bulkupdate_status', ['uses' => 'Backend\SplashScreenController@bulkchangeStatus'])->name('changeStatusSplashScreens');

		//TutorialScreen Module
		Route::get('/tutorialscreens/list', ['uses' => 'Backend\TutorialScreenController@index'])->name('tutorialscreens');
		Route::post('/tutorialscreens', ['uses' => 'Backend\TutorialScreenController@tutorialscreenAjax'])->name('tutorialscreenAjax');
		Route::get('/tutorialscreens/add', ['uses' => 'Backend\TutorialScreenController@create'])->name('createTutorialScreen');
		Route::get('/tutorialscreens/checkTutorialScreen/{id?}', ['uses' => 'Backend\TutorialScreenController@checkTutorialScreen'])->name('checkTutorialScreen');
		Route::post('/tutorialscreens/store', ['uses' => 'Backend\TutorialScreenController@store'])->name('addTutorialScreen');
		Route::get('/tutorialscreens/view/{id?}', ['uses' => 'Backend\TutorialScreenController@show'])->name('viewTutorialScreen');
		Route::get('/tutorialscreens/edit/{id?}', ['uses' => 'Backend\TutorialScreenController@edit'])->name('editTutorialScreen');
		Route::post('/tutorialscreens/update/{id?}', ['uses' => 'Backend\TutorialScreenController@update'])->name('updateTutorialScreen');
		Route::post('/tutorialscreens/update_status', ['uses' => 'Backend\TutorialScreenController@updateStatus'])->name('changeStatusTutorialScreen');
		Route::post('/tutorialscreens/update_statusAjax', ['uses' => 'Backend\TutorialScreenController@updateStatusAjax'])->name('changeStatusAjaxTutorialScreen');
		Route::post('/tutorialscreens/delete', ['uses' => 'Backend\TutorialScreenController@destroy'])->name('deleteTutorialScreen');
		Route::post('/tutorialscreens/bulkdelete', ['uses' => 'Backend\TutorialScreenController@bulkdelete'])->name('deleteTutorialScreens');
		Route::post('/tutorialscreens/bulkupdate_status', ['uses' => 'Backend\TutorialScreenController@bulkchangeStatus'])->name('changeStatusTutorialScreens');



		//Testimonial Module
		Route::get('/testimonials/list', ['uses' => 'Backend\TestimonialController@index'])->name('testimonials');
		Route::post('/testimonialAjax', ['uses' => 'Backend\TestimonialController@testimonialAjax'])->name('testimonialAjax');
		Route::get('/testimonials/add', ['uses' => 'Backend\TestimonialController@create'])->name('createTestimonial');
		Route::get('/testimonials/checkTestimonial/{id?}', ['uses' => 'Backend\TestimonialController@checkTestimonial'])->name('checkTestimonial');
		Route::post('/testimonials/store', ['uses' => 'Backend\TestimonialController@store'])->name('addTestimonial');
		Route::get('/testimonials/view/{id?}', ['uses' => 'Backend\TestimonialController@show'])->name('viewTestimonial');
		Route::get('/testimonials/edit/{id?}', ['uses' => 'Backend\TestimonialController@edit'])->name('editTestimonial');
		Route::post('/testimonials/update/{id?}', ['uses' => 'Backend\TestimonialController@update'])->name('updateTestimonial');
		Route::post('/testimonials/update_status', ['uses' => 'Backend\TestimonialController@updateStatus'])->name('changeStatusTestimonial');
		Route::post('/testimonials/update_statusAjax', ['uses' => 'Backend\TestimonialController@updateStatusAjax'])->name('changeStatusAjaxTestimonial');
		Route::post('/testimonials/delete', ['uses' => 'Backend\TestimonialController@destroy'])->name('deleteTestimonial');
		Route::post('/testimonials/bulkdelete', ['uses' => 'Backend\TestimonialController@bulkdelete'])->name('deleteTestimonials');
		Route::post('/testimonials/bulkupdate_status', ['uses' => 'Backend\TestimonialController@bulkchangeStatus'])->name('changeStatusTestimonials');

		
		//Setting Module: Setting list
		Route::get('/settings/list', ['uses' => 'Backend\SettingController@index'])->name('settings');
		Route::post('/settings/update', ['uses' => 'Backend\SettingController@update'])->name('updateSetting');
		//Clear Record: Setting list
		Route::get('clears', ['uses' => 'Backend\SettingController@clears'])->name('clears');
        Route::post('clears/record', ['uses' => 'Backend\SettingController@clearRecord'])->name('clearRecord');


		//Setting Module: Setting list
		Route::get('/adminsetting/list', ['uses' => 'Backend\AdminSettingController@index'])->name('adminsettings');
		Route::post('/adminsetting/update', ['uses' => 'Backend\AdminSettingController@update'])->name('updateAdminSetting');


        //Newsletter Module
		Route::get('/newsletter/list', ['uses' => 'Backend\NewsletterController@index'])->name('newsletter');
		Route::get('/newsletterAjax', ['uses' => 'Backend\NewsletterController@newsletterAjax'])->name('newsletterAjax');
		Route::get('/newsletter/view/{id?}', ['uses' => 'Backend\NewsletterController@show'])->name('viewNewsletter');


		//ContactUs Module
		Route::get('/contactus/list', ['uses' => 'Backend\ContactUsController@index'])->name('contactus');
		Route::get('/contactusAjax', ['uses' => 'Backend\ContactUsController@contactusAjax'])->name('contactusAjax');
		Route::get('/contactus/view/{id?}', ['uses' => 'Backend\ContactUsController@show'])->name('viewContactUs');

		//permissions Module  
		Route::get('permissions/list', ['uses' => 'Backend\PermissionController@index'])->name('permissions');



		//notification Module  
		Route::get('notifications/list', ['uses' => 'Backend\NotificationController@index'])->name('notifications');
		Route::post('notificationsAjax', ['uses' => 'Backend\NotificationController@notificationsAjax'])->name('notificationsAjax');
		Route::post('/notifications/delete', ['uses' => 'Backend\NotificationController@destroy'])->name('deleteNotification');
		Route::post('/notifications/bulkdelete', ['uses' => 'Backend\NotificationController@bulkdelete'])->name('deleteNotifications');
		Route::get('notifications/readNotification/{id}', ['uses' => 'Backend\NotificationController@readNotification'])->name('readNotification');
		Route::get('notifications/clearAllNotification', ['uses' => 'Backend\NotificationController@clearAllNotification'])->name('clearAllNotification');


	});

});

Route::get('cron/job', 'HomeController@cronJob')->name('cronJob');
Route::get('{slug}', 'HomeController@getPage')->name('getPage');