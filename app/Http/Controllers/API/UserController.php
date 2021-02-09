<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword;
use App\Models\Binary;
use App\Models\CoinAuth;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

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

  /**
   * @param Request $request
   * @return JsonResponse
   * @throws ValidationException
   */
  public function updateName(Request $request): JsonResponse
  {
    $this->validate($request, [
      "name" => "required|string",
    ]);

    $user = User::find(Auth::id());
    $user->name = $request->input("name");
    $user->save();

    return response()->json(["message" => "name has been changed"]);
  }

  /**
   * @param Request $request
   * @return JsonResponse
   * @throws ValidationException
   */
  public function updateWallet(Request $request): JsonResponse
  {
    $this->validate($request, [
      "wallet" => ["required", function ($_, $value, $fail) {
        $walletValidity = Http::asForm()->get("https://sochain.com/api/v2/is_address_valid/DOGE/" . $value);
        if (!$walletValidity->ok() || (!$walletValidity->successful() && !$walletValidity->json()["data"]["is_valid"])) {
          $fail("Invalid Wallet Dax");
        }
      }],
    ]);

    $user = User::find(Auth::id());
    $user->wallet_dax = $request->input("wallet");
    $user->save();

    return response()->json(["message" => "wallet has been changed"]);
  }

  /**
   * @param Request $request
   * @return JsonResponse
   * @throws ValidationException
   */
  public function updatePassword(Request $request): JsonResponse
  {
    $this->validate($request, [
      "password" => "required|string|min:6|same:confirmation_password"
    ]);

    $user = User::find(Auth::id());
    $user->password = Hash::make($request->input("password"));
    $user->save();

    return response()->json(["message" => "password has been changed"]);
  }

  public function requestCode($email): JsonResponse
  {
    $code = rand(1000, 9999);
    Mail::to($email)->send(new ForgotPassword($code, $email));

    return response()->json([
      "uniqueCode" => $code,
      "message" => "email has been send to receive unique code",
    ]);
  }

  public function forgotPassword(Request $request)
  {

  }
}
