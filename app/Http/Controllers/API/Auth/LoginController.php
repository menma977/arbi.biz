<?php

namespace App\Http\Controllers\API\Auth;

use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HttpController;
use App\Models\CoinAuth;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
  /**
   * @param Request $request
   * @return JsonResponse
   */
  public function __invoke(Request $request)
  {
    if (filter_var($request->username, FILTER_VALIDATE_EMAIL)) {
      $type = 'email';
    } else {
      $type = 'username';
    }
    Logger::info("Login: " . $request->username . " attempt to login from (" . $request->ip() . ")");
    $request->validate([
      "username" => "required|string",
      "password" => "required"
    ]);
    try {
      if (Auth::attempt([$type => $request->username, 'password' => $request->password])) {
        Logger::info("Login: " . $request->username . " successful login from (" . $request->ip() . ")");
        if (Auth::user()->suspend) {
          Logger::info("Login: " . $request->ip() . " trying with suspended account " . $request->ip);
          return response()->json(["code" => 400, "message" => "Your account currently suspended"]);
        }
        foreach (Auth::user()->tokens as $id => $item) {
          $item->delete();
        }
        if ($user = Auth::user()) {
          $coinAccount = CoinAuth::where("user_id", "=", $user->id)->first();
          $updatedAt = Carbon::parse($coinAccount->updated_at);
          if ($updatedAt->subMonth()->diffInDays(Carbon::now()) >= 30) {
            Logger::info("Login: Refreshing 999doge Cookie for" . $user->username);
            $dogeRes = HttpController::post("Login", [
              "Username" => $coinAccount->username,
              "Password" => $coinAccount->password
            ], true);
            Logger::info($dogeRes);
            if ($dogeRes["code"] == 200) {
              $coinAccount->cookie = $dogeRes["data"]["SessionCookie"];
              $coinAccount->save();
              Logger::info("Login: Updating 999doge Cookie from" . $user->username);
            } else {
              Logger::info("Login: Fail to update 999doge Cookie from" . $user->username);
            }
          }
          Logger::info($request->username . " Successfully Login but return invalid user");
          // TODO: Change token name
          $user->token = $user->createToken('API.' . $user->username)->accessToken;
          return response()->json([
            "code" => 200,
            "user" => [
              "username" => $user->username,
              "email" => $user->email,
              "hasTradedReal" => Carbon::parse($user->trade_fake ?: "last month")->diffInDays(Carbon::now()) > 0,
              "hasTradedFake" => Carbon::parse($user->trade_real ?: "last month")->diffInDays(Carbon::now()) > 0,
              "isSuspended" => $user->suspend,
              "token" => $user->token,
              "cookie" => $coinAccount->cookie,
            ]
          ]);
        } else {
          Logger::error("Login: " . $request->username . " Successfully Login but return invalid user");
          return response()->json(['code' => 500, 'message' => 'Invalid user'], 500);
        }

        Logger::error("Login: " . $request->username . " Successfully Login but return invalid user");
        return response()->json(['code' => 500, 'message' => 'Invalid user'], 500);
      }

      Logger::warning("Login: " . $request->username . " failed login attempt from (" . $request->ip() . ")");
      return response()->json(["code" => 400, "message" => "Username and/or Password didn't match"], 400);
    } catch (Exception $e) {
      Logger::error('Login: [' . $e->getCode() . '] "' . $e->getMessage() . '" on line ' . $e->getTrace()[0]['line'] . ' of file ' . $e->getTrace()[0]['file']);
      return response()->json(['code' => 500, "message" => "Something happen at our end"]);
    }
  }
}
