<?php

use App\Http\Controllers\api\NovelController;
use App\Http\Controllers\api\WxUserController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('/novel')->group(function () {
    Route::get('/search', [NovelController::class,'search']);
    Route::get('/catalog', [NovelController::class,'catalog']);
    Route::get('/article', [NovelController::class,'article']);
    Route::get('/book_info', [NovelController::class,'book_info']);
    Route::get('/shelf', [NovelController::class,'shelf']);
    Route::any('/shelf/add', [NovelController::class,'addBookToShelf']);
    Route::any('/shelf/remove', [NovelController::class,'removeBookFromShelf']);
    Route::any('/shelf/check', [NovelController::class,'checkBookInShelf']);
});

Route::prefix('/wxusers')->group(function () {
    Route::any('/code2session/{code}', [WxUserController::class,'Code2Session']);
    Route::any('/autoRegister', [WxUserController::class,'registerByOpenId']);
    Route::any('/getCurrentUser', [WxUserController::class,'getCurrentUser']);
    Route::any('/updateUser', [WxUserController::class,'updateUser']);
    Route::any('/updateUserPhone', [WxUserController::class,'updateUserPhone']);
});
