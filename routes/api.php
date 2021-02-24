<?php

use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\LogoutController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\BinaryController;
use App\Http\Controllers\API\Bot\FakeController;
use App\Http\Controllers\API\Bot\MartiAngelController;
use App\Http\Controllers\API\BroadcastAuthController;
use App\Http\Controllers\API\TicketController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\InfoController;
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
      Route::post('password', [UserController::class, "updatePassword"]);
      Route::post('pin', [UserController::class, "updatePin"]);
    });
  });

  Route::group(['prefix' => 'bot', 'as' => 'bot.'], function () {
    Route::post('fake', [FakeController::class, 'index'])->middleware(['throttle:1,1']);
    Route::post('marti/angel', [MartiAngelController::class, 'index']);
    Route::get('marti/angel/store/{balance}/{isWin}', [MartiAngelController::class, 'store']);
  });

  Route::group(['prefix' => 'binary', 'as' => 'user.binary.'], function () {
    Route::get("index", [BinaryController::class, 'index'])->name('index');
    Route::get("show/{id}", [BinaryController::class, 'show'])->name('show');
  });

  Route::group(['prefix' => 'pin', 'as' => 'user.pin.'], function () {
    Route::get("index", [TicketController::class, 'index'])->name('index');
    Route::post("store", [TicketController::class, 'store'])->name('store');
  });
});
