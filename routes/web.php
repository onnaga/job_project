<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::controller(AdminController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('add_admin', 'register');
    Route::post('delete_admin', 'deleteAdmin');
    Route::post('logout', 'logout');
    Route::get('get_offers','getAllOffers');
    Route::get('companies','getAllCompanies');
    Route::get('specializations','getAllSpecializations');

});

Route::get('/', function () {
    return view('login');
});
