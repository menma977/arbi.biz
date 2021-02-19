<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HttpController;
use App\Models\CoinAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class WithdrawController extends Controller
{
  /**
   * @return JsonResponse
   */
  public function all(Request $request): JsonResponse
  {
    $request->validate([
      "wallet" => ["required", function ($_, $value, $fail) {
        $walletValidity = Http::asForm()->get("https://sochain.com/api/v2/is_address_valid/DOGE/" . $value);
        if (!$walletValidity->ok() || (!$walletValidity->successful() && !$walletValidity->json()["data"]["is_valid"])) {
          $fail("Invalid Wallet");
        }
      }],
    ]);
    $response = self::withdraw(Auth::id(), $request->wallet, 0);
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
      "amount" => "required|numeric",
      "wallet" => ["required", function ($_, $value, $fail) {
        $walletValidity = Http::asForm()->get("https://sochain.com/api/v2/is_address_valid/DOGE/" . $value);
        if (!$walletValidity->ok() || (!$walletValidity->successful() && !$walletValidity->json()["data"]["is_valid"])) {
          $fail("Invalid Wallet");
        }
      }],
    ]);
    $response = self::withdraw(Auth::id(), $request->wallet, $request->amount);
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
  private static function withdraw($userId, $to, $amount): Collection
  {
    $coinAuth = CoinAuth::where("user_id", "=", $userId);
    return HttpController::post("Withdraw", [
      "s" => $coinAuth->cookie,
      "Amount" => $amount,
      "Address" => $to,
      "Currency" => "doge",
    ], true);
  }
}
