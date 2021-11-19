<?php

use App\Http\Controllers\AnticollectorController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\CPAController;
use App\Http\Controllers\PayboxController;
use App\Http\Controllers\UrlController;
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
Route::get('/fourthStage',[UserController::class,'fourthStage']);

Route::get('/stepOne',[UserController::class,'stepOne']);
Route::get('/stepTwo',[UserController::class,'stepTwo']);
Route::get('/stepThree',[UserController::class,'stepThree']);
Route::get('/stepThreeUrl',[UserController::class,'stepThreeUrl']);

Route::get('/addFeedback',[ComplaintController::class,'addFeedback']);
Route::get('/getFeedback',[ComplaintController::class,'getFeedback']);

Route::post('/getData',[UserController::class,'getData']);

Route::post('/getUserDataDeal',[UserController::class,'getUserDataDeal']);
Route::post('/getUserData',[UserController::class,'getUserData']);
Route::get('/stageDeal',[UserController::class,'stageDeal']);
Route::get('/leadgid',[CPAController::class,'leadgid']);
Route::get('/leadgidFree',[CPAController::class,'leadgidFree']);
Route::get('/signDoc',[UrlController::class,'signDoc']);
Route::get('/getDataSign',[UrlController::class,'getDataSign']);
Route::get('/removeShortUrl',[UrlController::class,'removeShortUrl']);
//paybox
Route::post('/makePayment',[PayboxController::class,'makePayment']);
Route::post('/paymentResult',[PayboxController::class,'paymentResult'])->name('payment-result');


//anticollector
Route::post('/firstStep',[AnticollectorController::class,'firstStep']);
Route::get('/checkCode',[AnticollectorController::class,'checkCode']);
Route::post('/secondStep',[AnticollectorController::class,'secondStep']);
Route::post('/sendMessage',[AnticollectorController::class,'sendMessage']);
Route::post('/lastStep',[AnticollectorController::class,'lastStep']);
Route::post('/signIn',[UserController::class,'signIn']);
Route::post('/getDocumentLink',[AnticollectorController::class,'getDocumentLink']);
Route::post('/getPush',[AnticollectorController::class,'getPush']);
Route::post('/uploadDocuments',[AnticollectorController::class,'uploadDocuments']);

