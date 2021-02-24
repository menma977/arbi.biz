<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword;
use App\Models\Bank;
use App\Models\Binary;
use App\Models\CoinAuth;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
    $ticketSpent = $userTicket->sum("credit");
    $ticketOwned = $userTicket->sum("debit") - $ticketSpent;
    $coinAuth = CoinAuth::where("user_id", "=", $user->id)->first();
    $binaries = Binary::select(["down_line as id", "users.username as username"])->where("sponsor", "=", $user->id)->join("users", "binaries.down_line", "=", "users.id")->get();
    $myBin = Binary::where("down_line", "=", $user->id)->first();
    if ($myBin) {
      $sponsorBinary = User::find($myBin->sponsor);
    } else {
      $sponsorBinary = User::find(1);
    }
    $setting = Setting::first();
    $bank = Bank::first();
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
      "wallet" => $coinAuth->wallet,
      "totalPin" => number_format($ticketOwned, 8, '', ''),
      "pinSpent" => number_format($ticketSpent, 8, '', ''),
      "totalDownLine" => $binaries->count(),
      "downLines" => $binaries,
      "sponsorId" => $sponsorBinary->id,
      "sponsor" => $sponsorBinary->username,
      "min_bot" => $setting->min_bot,
      "max_bot" => $setting->max_bot,
      "wallet_bank" => $bank->wallet,
      "it" => $setting->it,
      "buy_wall" => $setting->buy_wall,
      "sponsor_share" => $setting->sponsor,
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

  /**
   * @param Request $request
   * @return JsonResponse
   * @throws ValidationException
   */
  public function updatePin(Request $request): JsonResponse
  {
    $this->validate($request, [
      "pin" => "required|numeric|min:6|same:confirmation_pin"
    ]);

    $user = User::find(Auth::id());
    $user->pin = Hash::make($request->input("pin"));
    $user->save();

    return response()->json(["message" => "pin has been changed"]);
  }

  /**
   * @param Request $request
   * @return JsonResponse
   * @throws ValidationException
   */
  public function requestCode(Request $request): JsonResponse
  {
    $this->validate($request, [
      "email" => "required|email|exists:users,email",
    ]);

    $code = random_int(1000, 9999);
    Mail::to($request->input("email"))->send(new ForgotPassword($code, $request->input("email")));

    return response()->json([
      "uniqueCode" => $code,
      "message" => "email has been send to receive unique code",
    ]);
  }

  /**
   * @param Request $request
   * @return JsonResponse
   * @throws ValidationException
   */
  public function forgotPassword(Request $request): JsonResponse
  {
    $this->validate($request, [
      "email" => "required|email|exists:users,email",
      "password" => "required|string|min:6|same:confirmation_password"
    ]);
    $user = User::where("email", $request->input("email"))->first();
    $user->password = Hash::make($request->input("password"));
    $user->save();

    return response()->json(["message" => "password has been changed"]);
  }
}
