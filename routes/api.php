<?php

use App\Http\Controllers\BlogCommentController;
use App\Http\Controllers\BlogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

Route::prefix('user')->group(function() {
    Route::post('register',[UserController::class,'register']);
    Route::post('login',[UserController::class,'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('user')->group(function() {
        Route::get('profile',[UserController::class,'profile']);
        Route::post('logout',[UserController::class,'logout']);
    });

    Route::post('blog/react',[BlogController::class,'react']);
    Route::post('comment/react',[BlogCommentController::class,'react']);

    Route::apiResource('blog',BlogController::class);
    Route::apiResource('comment',BlogCommentController::class);
});
