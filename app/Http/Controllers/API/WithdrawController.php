<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HttpController;
use App\Models\CoinAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class WithdrawController extends Controller
{
  /**
   * @return JsonResponse
   */
  public function all(): JsonResponse
  {
    $response = self::withdraw(Auth::id(), 0);
    if ($response["code"] < 400) {
      return response()->json([
        "message" => "Success"
      ]);
    }

    return response()->json($response, $response["code"]);
  }

  /**
   * @param Request $request
   * @return JsonResponse
   */
  public function partial(Request $request): JsonResponse
  {
    $request->validate([
      "amount" => "required|numeric"
    ]);
    $response = self::withdraw(Auth::id(), $request->amount);
    if ($response["code"] < 400) {
      return response()->json([
        "message" => "Success"
      ]);
    }

    return response()->json($response, $response["code"]);
  }

  /**
   * @param $userId
   * @param $amount
   * @return Collection
   */
  private static function withdraw($userId, $amount): Collection
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
