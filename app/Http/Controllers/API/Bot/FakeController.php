<?php

namespace App\Http\Controllers\API\Bot;

use App\Events\TredingEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HttpController;
use App\Http\Controllers\ToolController;
use App\Models\Bank;
use App\Models\Binary;
use App\Models\CoinAuth;
use App\Models\Queue;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class FakeController extends Controller
{
  /**
   * @param Request $request
   * @return JsonResponse
   * @throws ValidationException
   */
  public function index(Request $request): JsonResponse
  {
    $user = Auth::user();
    $coinAuth = CoinAuth::where('user_id', $user->id)->first();
    $bank = Bank::find(1);
    if (Carbon::parse($user->trade_fake ?: "last month")->diffInDays(Carbon::now()) < 1) {
      return response()->json(['message' => 'Already trade today'], 500);
    }

    $setting = Setting::find(1);
    $balanceForValidate = number_format($request->input("balance") / 10 ** 8, 8, '.', '');
    $min_bot = number_format($setting->min_bot / 10 ** 8, 8, '.', '');
    $max_bot = number_format($setting->max_bot / 10 ** 8, 8, '.', '');
    if ($balanceForValidate < $min_bot) {
      return response()->json(['message' => "The balance must be at least $min_bot"], 500);
    }

    if ($balanceForValidate > $max_bot) {
      return response()->json(['message' => "The balance must be at least above $max_bot"], 500);
    }
    $this->validate($request, [
      'balance' => 'required|numeric',
    ]);

    if (!$coinAuth->cookie) {
      $coinAuth->cookie = self::getCookie($coinAuth->username, $coinAuth->password);
      if ($coinAuth->cookie !== "break") {
        $coinAuth->save();
      }
    }

    if (!$bank->cookie) {
      $bank->cookie = self::getCookie($bank->username, $bank->password);
      if ($bank->cookie !== "break") {
        $bank->save();
      }
    }

    $balancePool = self::getBalance($bank->cookie);
    $userBalance = round($request->balance * 0.05);
    if ($balancePool["code"] === 200) {
      if ($userBalance > ($balancePool["balance"] - Queue::where('send', false)->sum('value')) || $balancePool["balance"] < 1000) {
        $data = [
          's' => $coinAuth->cookie,
          'Amount' => $request->balance,
          'Address' => $bank->wallet,
          'Currency' => 'doge'
        ];
        $post = HttpController::post('Withdraw', $data);
        if ($post["code"] === 200) {
          $getPrice = HttpController::dogePrice();
          if ($getPrice["code"] === 200) {
            $receiveTicket = number_format(($getPrice["data"] * ($request->balance / 10 ** 8)) / 500000, 8, '.', '');
            ToolController::loseBot($user->id, $receiveTicket);

            $user->trade_fake = Carbon::now()->format("Y-m-d");
            $user->save();

            $bot_one = $user->trade_fake == Carbon::now()->format("Y-m-d");
            $bot_two = $user->trade_real == Carbon::now()->format("Y-m-d");
            TredingEvent::dispatch(Auth::user()->username, $bot_one, $bot_two);

            return response()->json(['message' => "LOSE"]);
          }

          return response()->json(['message' => $getPrice["message"]], 500);
        }

        return response()->json(['message' => $post["message"]], 500);
      }

      $shareIt = $userBalance * Setting::first()->it;
      $buyWall = $userBalance * Setting::first()->buy_wall;
      $sponsor = $userBalance * Setting::first()->sponsor;
      $remainingBalance = round($userBalance - ($shareIt + $buyWall + $sponsor));

      $post = HttpController::post('Withdraw', [
        's' => $bank->cookie,
        'Amount' => $remainingBalance,
        'Address' => $coinAuth->wallet,
        'Currency' => 'doge'
      ]);

      if ($post['code'] === 200) {
        $queue = new Queue();
        $queue->type = 'buy_wall';
        $queue->user_id = 1;
        $queue->value = $buyWall + $shareIt;
        $queue->send = false;
        $queue->save();

        $queue = new Queue();
        $queue->type = 'sponsor';
        $queue->user_id = Binary::where('down_line', $user->id)->first()->sponsor ?? 1;
        $queue->value = $sponsor;
        $queue->send = false;
        $queue->save();

        $user->trade_fake = Carbon::now()->format("Y-m-d");
        $user->save();

        $bot_one = Carbon::parse($user->trade_fake ?: "last month")->diffInDays(Carbon::now()) < 1;
        $bot_two = Carbon::parse($user->trade_real ?: "last month")->diffInDays(Carbon::now()) < 1;
        event(new TredingEvent(Auth::user()->username, $bot_one, $bot_two));

        return response()->json(['message' => "WIN"]);
      }

      return response()->json(['message' => "access rejected. you can try again"], 500);
    }

    return response()->json(['message' => $balancePool["message"]], 500);
  }

  /**
   * @param $cookie
   * @return array
   */
  public static function getBalance($cookie): array
  {
    $data = [
      's' => $cookie,
      'Currency' => 'doge'
    ];

    $post = HttpController::post('GetBalance', $data);
    if ($post["code"] === 200) {
      return [
        'code' => 200,
        'message' => 'success load balance',
        'balance' => $post['data']['Balance'],
      ];
    }

    return [
      'code' => 500,
      'message' => 'failed load balance',
      'balance' => 0,
    ];
  }

  /**
   * @param $username
   * @param $password
   * @return string
   */
  private static function getCookie($username, $password): string
  {
    $data = [
      'username' => $username,
      'password' => $password,
    ];
    $post = HttpController::post("Login", $data, true);
    if ($post['code'] === 200) {
      return $post['data']['SessionCookie'];
    }
    return "break";
  }
}
