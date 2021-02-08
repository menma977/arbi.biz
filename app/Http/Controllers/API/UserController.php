<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Binary;
use App\Models\CoinAuth;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
  /**
   * @return JsonResponse
   */
  public function index(): JsonResponse
  {
    $user = User::find(Auth::id());
    $userTicket = Ticket::where("user_id", $user->id);
    $ticketSpent = $userTicket->where("credit", ">", 0)->sum("credit");
    $ticketOwned = $userTicket->where("debit", ">", 0)->sum("debit") - $ticketSpent;
    $coinAuth = CoinAuth::where("user_id", "=", $user->id)->first();
    $binaries = Binary::select(["down_line as id", "users.username as username"])->where("sponsor", "=", $user->id)->join("users", "binaries.down_line", "=", "users.id")->get();
    $myBin = Binary::where("down_line", "=", $user->id)->first();
    if ($myBin) {
      $sponsorBinary = User::find($myBin->sponsor);
    } else {
      $sponsorBinary = User::find(1);
    }

    return response()->json([
      "code" => 200,
      "username" => $user->username,
      "name" => $user->name,
      "email" => $user->email,
      "hasTradedReal" => Carbon::parse($user->trade_real ?: "last month")->diffInDays(Carbon::now()) < 1,
      "hasTradedFake" => Carbon::parse($user->trade_fake ?: "last month")->diffInDays(Carbon::now()) < 1,
      "lastTradeReal" => $user->trade_real ? Carbon::parse($user->trade_real)->format("d-m-y h:m:s") : false,
      "lastTradeFake" => $user->trade_fake ? Carbon::parse($user->trade_fake)->format("d-m-y h:m:s") : false,
      "cookie" => $coinAuth->cookie,
      "walletDax" => $coinAuth->wallet_dax,
      "totalPin" => $ticketOwned,
      "pinSpent" => $ticketSpent,
      "totalDownLine" => $binaries->count(),
      "downLines" => $binaries,
      "sponsorId" => $sponsorBinary->id,
      "sponsor" => $sponsorBinary->username
    ]);
  }
}
