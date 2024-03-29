<?php

use App\Http\Controllers\FinanceController;
use App\Http\Controllers\RedisController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('/redis')->group(function () {
    Route::get('/index', [RedisController::class,'index']);
    Route::get('/index2', [RedisController::class,'index2']);
});

Route::prefix('/finance')->group(function () {
    Route::get('/', [FinanceController::class,'index']);
});