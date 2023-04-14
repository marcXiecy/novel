<?php

use App\Http\Controllers\api\EnumController;
use App\Http\Controllers\api\Novel56Controller;
use App\Http\Controllers\api\NovelBiqugeB5200Controller;
use App\Http\Controllers\api\NovelBiquge5200Controller;
use App\Http\Controllers\api\NovelBiqugeController;
use App\Http\Controllers\api\NovelbiqugeFController;
use App\Http\Controllers\api\NovelController;
use App\Http\Controllers\api\NovelCV148Controller;
use App\Http\Controllers\api\NovelDingDianController;
use App\Http\Controllers\api\NovelGonbController;
use App\Http\Controllers\api\NovelQbiqugeController;
use App\Http\Controllers\api\WxUserController;
use App\Http\Controllers\FinanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Psy\Sudo;

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


Route::prefix('/novel/biquge/')->group(function () {
    Route::get('/search', [NovelBiqugeController::class,'search']);
    Route::get('/catalog', [NovelBiqugeController::class,'catalog']);
    Route::get('/saveCatalog', [NovelBiqugeController::class,'saveCatalog']);
    Route::get('/article', [NovelBiqugeController::class,'article']);
    Route::get('/book_info', [NovelBiqugeController::class,'book_info']);
    Route::get('/shelf', [NovelBiqugeController::class,'shelf']);
    Route::any('/shelf/add', [NovelBiqugeController::class,'addBookToShelf']);
    Route::any('/shelf/remove', [NovelBiqugeController::class,'removeBookFromShelf']);
    Route::any('/shelf/check', [NovelBiqugeController::class,'checkBookInShelf']);
    Route::any('/saveCatalog', [NovelBiqugeController::class,'saveCatalog']);
});

Route::prefix('/novel/xbiquge5200/')->group(function () {
    Route::get('/search', [NovelBiquge5200Controller::class,'search']);
    Route::get('/catalog', [NovelBiquge5200Controller::class,'catalog']);
    Route::get('/saveCatalog', [NovelBiquge5200Controller::class,'saveCatalog']);
    Route::get('/article', [NovelBiquge5200Controller::class,'article']);
    Route::get('/book_info', [NovelBiquge5200Controller::class,'book_info']);
    Route::get('/shelf', [NovelBiquge5200Controller::class,'shelf']);
    Route::any('/shelf/add', [NovelBiquge5200Controller::class,'addBookToShelf']);
    Route::any('/shelf/remove', [NovelBiquge5200Controller::class,'removeBookFromShelf']);
    Route::any('/shelf/check', [NovelBiquge5200Controller::class,'checkBookInShelf']);
    Route::any('/saveCatalog', [NovelBiquge5200Controller::class,'saveCatalog']);
});

Route::prefix('/novel/biqu5200/')->group(function () {
    Route::get('/search', [NovelBiqugeB5200Controller::class,'search']);
    Route::get('/catalog', [NovelBiqugeB5200Controller::class,'catalog']);
    Route::get('/saveCatalog', [NovelBiqugeB5200Controller::class,'saveCatalog']);
    Route::get('/article', [NovelBiqugeB5200Controller::class,'article']);
    Route::get('/book_info', [NovelBiqugeB5200Controller::class,'book_info']);
    Route::get('/shelf', [NovelBiqugeB5200Controller::class,'shelf']);
    Route::any('/shelf/add', [NovelBiqugeB5200Controller::class,'addBookToShelf']);
    Route::any('/shelf/remove', [NovelBiqugeB5200Controller::class,'removeBookFromShelf']);
    Route::any('/shelf/check', [NovelBiqugeB5200Controller::class,'checkBookInShelf']);
    Route::any('/saveCatalog', [NovelBiqugeB5200Controller::class,'saveCatalog']);
});

Route::prefix('/novel/dingdian/')->group(function () {
    Route::get('/search', [NovelDingDianController::class,'search']);
    Route::get('/catalog', [NovelDingDianController::class,'catalog']);
    Route::get('/saveCatalog', [NovelDingDianController::class,'saveCatalog']);
    Route::get('/article', [NovelDingDianController::class,'article']);
    Route::get('/book_info', [NovelDingDianController::class,'book_info']);
    Route::get('/shelf', [NovelDingDianController::class,'shelf']);
    Route::any('/shelf/add', [NovelDingDianController::class,'addBookToShelf']);
    Route::any('/shelf/remove', [NovelDingDianController::class,'removeBookFromShelf']);
    Route::any('/shelf/check', [NovelDingDianController::class,'checkBookInShelf']);
    Route::any('/saveCatalog', [NovelDingDianController::class,'saveCatalog']);
});

Route::prefix('/novel/qbiquge/')->group(function () {
    Route::get('/search', [NovelQbiqugeController::class,'search']);
    Route::get('/catalog', [NovelQbiqugeController::class,'catalog']);
    Route::get('/saveCatalog', [NovelQbiqugeController::class,'saveCatalog']);
    Route::get('/article', [NovelQbiqugeController::class,'article']);
    Route::get('/book_info', [NovelQbiqugeController::class,'book_info']);
    Route::get('/shelf', [NovelQbiqugeController::class,'shelf']);
    Route::any('/shelf/add', [NovelQbiqugeController::class,'addBookToShelf']);
    Route::any('/shelf/remove', [NovelQbiqugeController::class,'removeBookFromShelf']);
    Route::any('/shelf/check', [NovelQbiqugeController::class,'checkBookInShelf']);
    Route::any('/saveCatalog', [NovelQbiqugeController::class,'saveCatalog']);
});

Route::prefix('/novel/cv148/')->group(function () {
    Route::get('/search', [NovelCV148Controller::class,'search']);
    Route::get('/catalog', [NovelCV148Controller::class,'catalog']);
    Route::get('/saveCatalog', [NovelCV148Controller::class,'saveCatalog']);
    Route::get('/article', [NovelCV148Controller::class,'article']);
    Route::get('/book_info', [NovelCV148Controller::class,'book_info']);
    Route::get('/shelf', [NovelCV148Controller::class,'shelf']);
    Route::any('/shelf/add', [NovelCV148Controller::class,'addBookToShelf']);
    Route::any('/shelf/remove', [NovelCV148Controller::class,'removeBookFromShelf']);
    Route::any('/shelf/check', [NovelCV148Controller::class,'checkBookInShelf']);
    Route::any('/saveCatalog', [NovelCV148Controller::class,'saveCatalog']);
});

Route::prefix('/novel/fyrsks/')->group(function () {
    Route::get('/search', [NovelbiqugeFController::class,'search']);
    Route::get('/catalog', [NovelbiqugeFController::class,'catalog']);
    Route::get('/saveCatalog', [NovelbiqugeFController::class,'saveCatalog']);
    Route::get('/article', [NovelbiqugeFController::class,'article']);
    Route::get('/book_info', [NovelbiqugeFController::class,'book_info']);
    Route::get('/shelf', [NovelbiqugeFController::class,'shelf']);
    Route::any('/shelf/add', [NovelbiqugeFController::class,'addBookToShelf']);
    Route::any('/shelf/remove', [NovelbiqugeFController::class,'removeBookFromShelf']);
    Route::any('/shelf/check', [NovelbiqugeFController::class,'checkBookInShelf']);
    Route::any('/saveCatalog', [NovelbiqugeFController::class,'saveCatalog']);
});

Route::prefix('/novel/56shuku/')->group(function () {
    Route::get('/search', [Novel56Controller::class,'search']);
    Route::get('/catalog', [Novel56Controller::class,'catalog']);
    Route::get('/saveCatalog', [Novel56Controller::class,'saveCatalog']);
    Route::get('/article', [Novel56Controller::class,'article']);
    Route::get('/book_info', [Novel56Controller::class,'book_info']);
    Route::get('/shelf', [Novel56Controller::class,'shelf']);
    Route::any('/shelf/add', [Novel56Controller::class,'addBookToShelf']);
    Route::any('/shelf/remove', [Novel56Controller::class,'removeBookFromShelf']);
    Route::any('/shelf/check', [Novel56Controller::class,'checkBookInShelf']);
    Route::any('/saveCatalog', [Novel56Controller::class,'saveCatalog']);
});

Route::prefix('/novel/gonb/')->group(function () {
    Route::get('/search', [NovelGonbController::class,'search']);
    Route::get('/catalog', [NovelGonbController::class,'catalog']);
    Route::get('/saveCatalog', [NovelGonbController::class,'saveCatalog']);
    Route::get('/article', [NovelGonbController::class,'article']);
    Route::get('/book_info', [NovelGonbController::class,'book_info']);
    Route::get('/shelf', [NovelGonbController::class,'shelf']);
    Route::any('/shelf/add', [NovelGonbController::class,'addBookToShelf']);
    Route::any('/shelf/remove', [NovelGonbController::class,'removeBookFromShelf']);
    Route::any('/shelf/check', [NovelGonbController::class,'checkBookInShelf']);
    Route::any('/saveCatalog', [NovelGonbController::class,'saveCatalog']);
});

Route::prefix('/wxusers')->group(function () {
    Route::any('/checkSession', [WxUserController::class,'checkSession']);
    Route::any('/code2session/{code}', [WxUserController::class,'Code2Session']);
    Route::any('/autoRegister', [WxUserController::class,'registerByOpenId']);
    Route::any('/getCurrentUser', [WxUserController::class,'getCurrentUser']);
    Route::any('/updateUser', [WxUserController::class,'updateUser']);
    Route::any('/updateUserPhone', [WxUserController::class,'updateUserPhone']);
});

Route::prefix('/enums')->group(function () {
    Route::any('/sources', [EnumController::class,'sources']);
});

Route::prefix('/snow')->group(function () {
    Route::any('/', [EnumController::class,'sources']);
});

Route::prefix('/finance')->group(function () {
    Route::any('/analysis', [FinanceController::class,'analysis']);
    Route::any('/clear', [FinanceController::class,'clear']);
    Route::any('/set', [FinanceController::class,'set']);
    Route::any('/excel/import', [FinanceController::class,'importExcel']);
});