<?php

use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\UserController;
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
Route::get('/identification',[UserController::class,'identification']);
Route::get('/stepOne',[UserController::class,'stepOne']);
Route::get('/stepTwo',[UserController::class,'stepTwo']);
Route::get('/stepThree',[UserController::class,'stepThree']);

Route::get('/addFeedback',[ComplaintController::class,'addFeedback']);
Route::get('/getFeedback',[ComplaintController::class,'getFeedback']);

Route::post('/getData',[UserController::class,'getData']);
