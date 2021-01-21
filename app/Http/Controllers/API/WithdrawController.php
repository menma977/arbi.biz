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
    $res = self::wd(Auth::id(), 0);
    if ($res["code"] < 400) {
      return response()->json([
        "message" => "Success"
      ]);
    } else {
      return response()->json($res, $res["code"]);
    }
  }

  public function partial(Request $request)
  {
    $res = self::wd(Auth::id(), $request->amount);
    if ($res["code"] < 400) {
      return response()->json([
        "message" => "Success"
      ]);
    } else {
      return response()->json($res, $res["code"]);
    }
  }

  private static function wd($userId, $amount)
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
