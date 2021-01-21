<?php

namespace App\Http\Controllers\API;

use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HttpController;
use App\Models\CoinAuth;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
  /**
   * @param Request $request
   * @return JsonResponse
   */
  public function login(Request $request)
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
              "tradeFake" => $user->trade_fake,
              "tradeReal" => $user->trade_real,
              "isSuspended" => $user->suspend,
              "cookie" => $coinAccount->cookie,
            ]
          ]);
        } else {
          Logger::error("Login: " . $request->username . " Successfully Login but return invalid user");
          return response()->json(['code' => 500, 'message' => 'Invalid user'], 500);
        }

        Logger::error("Register: " . $request->username . " Successfully Login but return invalid user");
        return response()->json(['code' => 500, 'message' => 'Invalid user'], 500);
      }

      Logger::warning($request->username . " failed login attempt from (" . $request->ip() . ")");
      return response()->json(["code" => 400, "message" => "Username and/or Password didn't match"], 400);
    } catch (Exception $e) {
      Logger::error('Login: [' . $e->getCode() . '] "' . $e->getMessage() . '" on line ' . $e->getTrace()[0]['line'] . ' of file ' . $e->getTrace()[0]['file']);
      return response()->json(['code' => 500, "message" => "Something happen at our end"]);
    }
  }

  public function register(Request $request)
  {
    Logger::info("Register: attempt from (" . $request->ip() . ")");
    $request->validate([
      "name" => "required|string",
      "username" => "required|unique:users,username",
      "email" => "required|unique:users,email",
      "password" => "required|same:confirmation_password|min:6",
      "wallet_dax" => ["required", function ($_, $value, $fail) {
        $walletValidity = Http::asForm()->get("https://sochain.com/api/v2/is_address_valid/DOGE/" . $value);
        if (!$walletValidity->ok() || (!$walletValidity->successful() && !$walletValidity->json()["data"]["is_valid"])) {
          $fail("Invalid Wallet Dax");
        }
      }]
    ]);
    try {
      $dogeAccount = HttpController::post("CreateAccount");
      if ($dogeAccount["code"] < 400) {
        $dogeAccount = $dogeAccount["data"];
        $username_coin = $this->randomStr();
        $password_coin = $this->randomStr();
        $dogeUser = HttpController::post("CreateUser", [
          "s" => $dogeAccount["SessionCookie"],
          "username" => $username_coin,
          "password" => $password_coin
        ]);
        if ($dogeUser["code"] < 400) {
          $dogeUser = $dogeUser["data"];
          $wallet = HttpController::post("GetDepositAddress", ['s' => $dogeAccount["SessionCookie"], 'Currency' => "doge"]);
          if ($wallet["code"] < 400) {
            $wallet = $wallet["data"];
            $user = new User([
              // TODO: Additional user field
              "name" => $request->name,
              "username" => $request->username,
              "email" => $request->email,
              "password" => Hash::make($request->password),
              "password_mirror" => $request->password,
              "suspend" => false
            ]);
            $user->save();
            $coinAuth = new CoinAuth([
              "wallet_dax" => $request->wallet_dax,
              "wallet" => $wallet["Address"],
              "username" => $username_coin,
              "password" => $password_coin,
              "cookie" => $dogeAccount["SessionCookie"],
            ]);
            $coinAuth->save();
            return response()->json(['code' => 200, "message" => "success"], 200);
            Logger::info("Register: " . $request->username . " from (" . $request->ip() . ") Registered successfully");
          } else {
            Logger::warning("Register: attempt from (" . $request->ip() . ") failed at fetching wallet");
            Logger::warning("Register: (" . $request->ip() . ") status: " . $dogeAccount["code"] . " message: ", $dogeAccount["message"]);
            return response()->json(['code' => 502, "message" => "Something happen at our end"], 502);
          }
        } else {
          Logger::warning("Register: attempt from (" . $request->ip() . ") failed at creating Doge User");
          Logger::warning("Register: (" . $request->ip() . ") status: " . $dogeAccount["code"] . " message: ", $dogeAccount["message"]);
          return response()->json(['code' => 502, "message" => "Something happen at our end"], 502);
        }
      } else {
        Logger::warning("Register: attempt from (" . $request->ip() . ") failed at creating Doge Account");
        Logger::warning("Register: (" . $request->ip() . ") status: " . $dogeAccount["code"] . " message: ", $dogeAccount["message"]);
        return response()->json(['code' => 502, "message" => "Something happen at our end"], 502);
      }
    } catch (Exception $e) {
      Logger::error('Register: [' . $e->getCode() . '] "' . $e->getMessage() . '" on line ' . $e->getTrace()[0]['line'] . ' of file ' . $e->getTrace()[0]['file']);
      return response()->json(['code' => 500, "message" => "Something happen at our end"]);
    }
  }

  public function logout(Request $request)
  {
    Logger::warning("Logout: attempt from " . Auth::user()->username . " failed at creating Doge Account");
    foreach (Auth::user()->tokens as $key => $value) {
      $value->delete();
    }
    return response('', 204);
  }

  private function randomStr($length = 20)
  {
    $pool = "qwertyuiopasdfghjklzxcvbnm1234567890QWERTYUIOPASDFGHJKLZXCVBNM";
    return substr(str_shuffle(str_repeat($pool, 5)), 0, $length) . Carbon::now()->format('YmdHis');
  }
}
