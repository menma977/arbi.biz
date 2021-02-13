<?php

namespace App\Http\Controllers\API\Auth;

use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HttpController;
use App\Http\Controllers\ToolController;
use App\Mail\Register;
use App\Models\Binary;
use App\Models\CoinAuth;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class RegisterController extends Controller
{

  private $PIN_SPENT_ON_REGISTER = 1;

  /**
   * @param Request $request
   * @return JsonResponse
   */
  public function __invoke(Request $request): JsonResponse
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

    $userTicket = Ticket::where("user_id", Auth::id());
    $ticketSpent = $userTicket->sum("credit");
    $ticketOwned = $userTicket->sum("debit") - $ticketSpent;
    if ($ticketOwned < $this->PIN_SPENT_ON_REGISTER) {
      return response()->json(["message" => "Insufficient PIN"], 400);
    }

    try {
      $dogeAccount = HttpController::post("CreateAccount", null, true);
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
            $binary = new Binary([
              'sponsor' => Auth::id(),
              'down_line' => $user->id,
            ]);
            $binary->save();
            $coinAuth = new CoinAuth([
              "user_id" => $user->id,
              "wallet_dax" => $request->wallet_dax,
              "wallet" => $wallet["Address"],
              "username" => $username_coin,
              "password" => $password_coin,
              "trade_fake" => Carbon::yesterday(),
              "trade_real" => Carbon::yesterday(),
              "cookie" => $dogeAccount["SessionCookie"],
            ]);
            $coinAuth->save();
            ToolController::register(Auth::id(), $this->PIN_SPENT_ON_REGISTER, $user->username);
            event(new Register($request->email, $request->username, $request->password, $wallet["Address"], $request->wallet_dax));
            Logger::info("Register: " . $request->username . " from (" . $request->ip() . ") Registered successfully");
            return response()->json(['code' => 200, "message" => "success"]);
          }

          Logger::warning("Register: attempt from (" . $request->ip() . ") failed at fetching wallet");
          Logger::warning("Register: (" . $request->ip() . ") status: " . $dogeAccount["code"] . " message: ", $dogeAccount["message"]);
          return response()->json(['code' => 502, "message" => "Something happen at our end"], 502);
        }

        Logger::warning("Register: attempt from (" . $request->ip() . ") failed at creating Doge User");
        Logger::warning("Register: (" . $request->ip() . ") status: " . $dogeAccount["code"] . " message: ", $dogeAccount["message"]);
        return response()->json(['code' => 502, "message" => "Something happen at our end"], 502);
      }

      Logger::warning("Register: attempt from (" . $request->ip() . ") failed at creating Doge Account");
      Logger::warning("Register: (" . $request->ip() . ") status: " . $dogeAccount["code"] . " message: ", $dogeAccount["message"]);
      return response()->json(['code' => 502, "message" => "Something happen at our end"], 502);
    } catch (Exception $e) {
      Logger::error('Register: [' . $e->getCode() . '] "' . $e->getMessage() . '" on line ' . $e->getTrace()[0]['line'] . ' of file ' . $e->getTrace()[0]['file']);
      return response()->json(['code' => 500, "message" => "Something happen at our end"], 500);
    }
  }

  private function randomStr($length = 20): string
  {
    $pool = "qwertyuiopasdfghjklzxcvbnm1234567890QWERTYUIOPASDFGHJKLZXCVBNM";
    return substr(str_shuffle(str_repeat($pool, 5)), 0, $length) . Carbon::now()->format('YmdHis');
  }
}
