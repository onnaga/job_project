<?php


use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompaniesPhotoController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PhotoController;



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
    Route::get('get_myComp_photo','showMine');



});




