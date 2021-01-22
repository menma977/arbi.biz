<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HttpController;
use App\Models\CoinAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawController extends Controller
{

  public function all()
  {
    $response = self::withdraw(Auth::id(), 0);
    if ($response["code"] < 400) {
      return response()->json([
        "message" => "Success"
      ]);
    }

    return response()->json($response, $response["code"]);
  }

  public function partial(Request $request)
  {
    $response = self::withdraw(Auth::id(), $request->amount);
    if ($response["code"] < 400) {
      return response()->json([
        "message" => "Success"
      ]);
    }

    return response()->json($response, $response["code"]);
  }

  private static function withdraw($userId, $amount)
  {
    $coinAuth = CoinAuth::where("user_id", "=", $userId);
    return HttpController::post("Withdraw", [
      "s" => $coinAuth->cookie,
      "Amount" => $amount,
      "Address" => $coinAuth->wallet_dax,
      "Currency" => "doge",
    ], true);
  }
}
