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
    } else if (filter_var($request->username, FILTER_VALIDATE_INT)) {
      $type = 'phone';
    } else {
      $type = 'username';
    }
    Logger::info("Login: " . $request->username . " attempt to login from (" . $request->ip() . ")");
    $request->validate([
      "username" => "required|string|in:users,$type",
      "password" => "required"
    ]);
    try {
      if (Auth::user()->suspend) {
        Logger::info("Login: " . $request->ip() . " trying with suspended account " . $request->ip);
        return response()->json(["code" => 400, "message" => "Your account currently suspended"]);
      }
      if (Auth::attempt([$type => $request->username, 'password' => $request->password])) {
        Logger::info("Login: " . $request->username . " successful login from (" . $request->ip() . ")");
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
            ]);
            if ($dogeRes->code == 200) {
              $coinAccount->cookie = $dogeRes["data"]["SessionCookie"];
              $coinAccount->save();
              Logger::info("Login: Updating 999doge Cookie from" . $user->username);
            } else {
              Logger::info("Login: Fail to update 999doge Cookie from" . $user->username);
            }
          }
          Logger::info($request->username . " Successfully Login but return invalid user");
          $user->token = $user->createToken('API.')->accessToken;
          // TODO: Additional Login value
          return response()->json([
            "code" => 200,
            "user" => [
              "username" => $user->username,
              "email" => $user->email,
              "phone" => $user->phone,
              "tradeFake" => Carbon::parse($coinAccount->trade_fake)->diffInDays(Carbon::now()) > 0,
              "tradeReal" => Carbon::parse($coinAccount->trade_real)->diffInDays(Carbon::now()) > 0,
              "isSuspended" => $user->suspend,
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
