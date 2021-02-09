<?php

use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\LogoutController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\BinaryController;
use App\Http\Controllers\API\Bot\FakeController;
use App\Http\Controllers\API\Bot\MartiAngelController;
use App\Http\Controllers\API\BroadcastAuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\InfoController;
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

Route::get('info', InfoController::class);
Route::post('login', LoginController::class)->middleware(['throttle:2,1', 'guest']);

Route::group(['prefix' => 'forgot', 'as' => 'forgot.'], function () {
  Route::post('email', [UserController::class, "requestCode"]);
  Route::post('update', [UserController::class, "forgotPassword"]);
});

Route::middleware(['auth:api'])->group(function () {
  Route::get('logout', LogoutController::class);
  Route::post('register', RegisterController::class);

  Route::post('broadcasting/auth', BroadcastAuthController::class);

  Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
    Route::get('profile', [UserController::class, "index"]);
    Route::group(['prefix' => 'update', 'as' => 'update.'], function () {
      Route::post('name', [UserController::class, "updateName"]);
      Route::post('wallet', [UserController::class, "updateWallet"]);
      Route::post('password', [UserController::class, "updatePassword"]);
    });
  });

  Route::group(['prefix' => 'bot', 'as' => 'bot.'], function () {
    Route::get('fake', [FakeController::class, 'index'])->middleware(['throttle:1,1']);
    Route::post('marti/angel', [MartiAngelController::class, 'index'])->middleware(['throttle:1,1']);
    Route::get('marti/angel/store/{balance}', [MartiAngelController::class, 'store']);
  });

  Route::group(['prefix' => 'binary', 'as' => 'user.binary.'], function () {
    Route::get("index", [BinaryController::class, 'index'])->name('index');
    Route::get("show", [BinaryController::class, 'show'])->name('show');
  });
});
