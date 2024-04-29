<?php


use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompaniesPhotoController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\NowWorkerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OffersController;
use App\Http\Controllers\UserOfferController;
use App\Models\now_worker;
use App\Models\offers;

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::get('me', 'me');
});


Route::controller(CompanyController::class)->group(function () {
    Route::post('compLogin', 'login');
    Route::post('compRegister', 'register');
    Route::post('compLogout', 'logout');
    Route::post('compRefresh', 'refresh');
    Route::get('compMe', 'me');
});

Route::controller(PhotoController::class)->group(function(){
    Route::post('set_photo','store');
    Route::get('get_my_photo','showMine');
});

Route::controller(CompaniesPhotoController::class)->group(function(){

    Route::post('set_comp_photo','store');
    Route::get('get_myComp_photo','showMine');

});


Route::controller(OrderController::class)->group(function(){
    //for users
    Route::post('new_order','make_order')->middleware('auth:api');
    Route::get('get_my_orders','show_all_Mine')->middleware('auth:api');
    Route::get('get_one_cv/{order_id}','show_specified_order')->middleware('auth:api');
    Route::delete('delete_order','delete_order')->middleware('auth:api');


    //for companies



    Route::get('get_my_orders_company','show_all_Mine_company')->middleware('auth:apiCompany');

    Route::get('get_user_orders','show_all_Mine_from_user')->middleware('auth:apiCompany');

    Route::get('get_one_cv_company','show_specified_cv')->middleware('auth:apiCompany');

    Route::post('company_answer','answer_to_order')->middleware('auth:apiCompany');
    //for admin



});



Route::controller(NowWorkerController::class)->group(function(){
    // for users
    Route::get('work_details','show');

    // for companies
    Route::post('set_my_worker','create');
    Route::post('update_my_worker','update')->middleware('auth:apiCompany');;
    Route::get('my_employees','show_employees');

});

Route::controller(NotificationController::class)->group(function(){
    // for users
    Route::get('get_user_notifications','show')->middleware('auth:api');



    // for companies
    Route::get('get_company_notifications','show_company')->middleware('auth:apiCompany');;


});

Route::controller(OffersController::class)->group(function(){

    Route::get('show_all_offers','show_all');
    Route::get('show_company_offers_for_user','show_company_orders');

    // for companies
    Route::post('add_offer','create')->middleware('auth:apiCompany');
    Route::get('show_my_offers','show')->middleware('auth:apiCompany');
    Route::post('update_offer','update')->middleware('auth:apiCompany');
    Route::delete('delete_my_offer','delete')->middleware('auth:apiCompany');

});
Route::controller(UserOfferController::class)->group(function(){
//for user
Route::get('accept_the_offer','accept_company_offer')->middleware('auth:api');
//for company
Route::get('offer_accepters','show_accepters')->middleware('auth:apiCompany');
Route::get('offer_orders','offer_orders')->middleware('auth:apiCompany');
});
