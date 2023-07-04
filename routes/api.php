<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\followController;
use App\Http\Controllers\flatformController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['prefix' => 'users'], function () {
    Route::get('/', 'UserController@index');
    Route::post('/', 'UserController@store');
    Route::get('/{id}', 'UserController@show');
    Route::put('/{id}', 'UserController@update');
    Route::delete('/{id}', 'UserController@destroy');
});

/*Route::group(['prefix' => 'homepage'], function () {
    Route::get('abc',[HomepageController::class, 'index']);
});*/
Route::prefix('homepage')->group(function () {
    Route::get('/', [HomepageController::class, 'index']);
    Route::get('/{name}', [HomepageController::class, 'show']);
    Route::get('/{name}/{id}', [HomepageController::class, 'show1']);
});
Route::prefix('login')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'Mail']);
    Route::post('/verifyEmail', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'Mailforgot']);
    Route::post('/verifyForgotEmail', [AuthController::class, 'verifyForgotEmail']);
    Route::post('/forgotPass', [AuthController::class, 'forgotPass']);
    Route::post('/resendOTP', [AuthController::class, 'resendOTP']);
});
Route::prefix('user')->group(function () {
    Route::get('/show', [AuthController::class, 'showAlluser']);
    Route::get('/show/{id}', [AuthController::class, 'showUser']);
    Route::get('/follower/{id}', [followController::class, 'show']);
});
Route::prefix('video')->group(function () {
    Route::get('/', [VideoController::class, 'index']);
    Route::get('/show', [VideoController::class, 'show1']); //showallvideo
    Route::get('/show/{id}', [VideoController::class, 'show']);
    Route::get('/show/user/{id}', [VideoController::class, 'showUserVideo']);
    // Route::get('/show/coop/{id}', [VideoController::class, 'showCoopcreation']);
});
////////////////////////////////////////////////////////////////////
Route::prefix('flatform')->group(function () {
    Route::get('/trending', [flatformController::class, 'trending']);
    Route::get('/foryou', [flatformController::class, 'foryou']);
    Route::get('/category', [flatformController::class, 'categories']);
});
Route::get('/videoPage/{id}', [VideoController::class, 'videopage']);
Route::get('/videoPage-co/{id}', [VideoController::class, 'showCoopcreation']); //
Route::get('/videoPage-cmt/{id}', [VideoController::class, 'showcmt']); //
//check
Route::prefix('check')->group(function () {
    Route::post('/likeVideo', [VideoController::class, 'CheckLikeVideo']);
    Route::post('/countShare', [VideoController::class, 'countShare']);
});

/// add count++
Route::prefix('add')->group(function () {
    Route::post('/addviewVideo', [VideoController::class, 'addview']);
    Route::post('/addlikeVideo', [VideoController::class, 'addlike']);
    Route::post('/addShareVideo', [VideoController::class, 'addshare']);
});