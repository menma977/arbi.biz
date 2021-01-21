<?php

use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\LogoutController;
use App\Http\Controllers\API\Auth\RegisterController;
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

Route::post('/login', LoginController::class)->middleware(['throttle:2,1', 'guest']);

Route::middleware(['auth:api', 'verified'])->group(function () {
  Route::get('/logout', LogoutController::class);
  Route::post('/register', RegisterController::class);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();
});
