<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InstallmentController;
use App\Models\Installment;

// Route::controller(AuthController::class)->group(function () {
//     Route::post('login', 'login');
//     Route::post('register', 'register');
//     Route::post('logout', 'logout');
//     Route::post('refresh', 'refresh');

// });

Route::group(['middleware' => ['api','throttle:60,1'],'prefix' => 'v1/auth'], function ($router) {
    Route::post('login',  [AuthController::class ,'login'])->name('login');
    Route::post('logout', [AuthController::class ,'logout']);
    Route::post('refresh',[AuthController::class ,'refresh']);
    Route::post('register', [AuthController::class ,'store']);
});

Route::group(['middleware' => ['jwt.verify','throttle:60,1'],'prefix' => 'v1/customers'], function ($router) {
    // Customer Route
    Route::get('index',  [CustomerController::class ,'index']);
    Route::post('create', [CustomerController::class , 'create']);
    Route::get('show/{id?}',[ CustomerController::class , 'show'])->name('show-customer');
    Route::post('delete', [CustomerController::class ,'destroy']);
});

Route::group(['middleware' => ['jwt.verify','throttle:60,1'],'prefix' => 'v1/installments'], function ($router) {
    // Customer Route
    Route::get('index',  [InstallmentController::class ,'index']);
    Route::post('create', [InstallmentController::class , 'create']);
    Route::post('search', [InstallmentController::class , 'searchByCustomer']);
    Route::get('show/{id?}',[ InstallmentController::class , 'show'])->name('show-customer');
    Route::post('delete', [InstallmentController::class ,'destroy']);
});
