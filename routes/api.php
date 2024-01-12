<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('register', [AuthController::class, 'register']);
});



Route::group(['middleware' => 'api', 'prefix' => 'messages'], function ($router){
    Route::get('chat/{party_id}', [MessageController::class, 'showPartyChat']);
    Route::post('chat/create', [MessageController::class, 'create']);
});

Route::group(['middleware' => 'api', 'prefix' => 'videos'], function ($router){
    Route::get('/', [VideoController::class, 'index']);
    Route::get('/{$id}', [VideoController::class, 'showVideo']);
    Route::post('/upload', [VideoController::class, 'upload']);
});


Route::group(['middleware' => 'api', 'prefix' => 'parties'], function ($router){
    Route::get('/', [PartyController::class, 'index']);
    Route::get('/{$id}', [PartyController::class, 'show']);
}); 