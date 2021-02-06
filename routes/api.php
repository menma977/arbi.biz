<?php

use App\Events\Announcement;
use App\Events\TicketEvent;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\LogoutController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Bot\FakeController;
use App\Http\Controllers\API\Bot\MartiAngelController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Pusher\Pusher;

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

Route::middleware(['auth:api'])->group(function () {
  Route::get('/logout', LogoutController::class);
  Route::post('/register', RegisterController::class);

  Route::get('/test', function () {
    TicketEvent::dispatch(Auth::user()->username, "421 asd");
    //event(new Announcement(Auth::user()->username, "421"));
    return response()->json(Auth::user());
  });

  Route::post('/broadcasting/auth', function (Request $request) {
    Log::debug($request->all());
    Log::debug(env("PUSHER_APP_KEY") . ":" . hash_hmac("SHA256", $request->post("socket_id") . $request->post("channel_name"), env("PUSHER_APP_SECRET")));
    return response()->json([
      "auth" => env("PUSHER_APP_KEY") . ":" . hash_hmac("SHA256", $request->post("socket_id") . $request->post("channel_name"), env("PUSHER_APP_SECRET"))
    ]);
  });

  Route::get('/my', [UserController::class, "index"]);

  Route::group(['prefix' => 'bot', 'as' => 'bot.'], function () {
    Route::get('/fake', [FakeController::class, 'index'])->middleware(['throttle:1,1']);
    Route::post('/marti/angel', [MartiAngelController::class, 'index'])->middleware(['throttle:1,1']);
  });
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();
});
