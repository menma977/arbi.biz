<?php

namespace App\Http\Controllers\API\Bot;

use App\Events\TredingEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HttpController;
use App\Http\Controllers\ToolController;
use App\Models\Bank;
use App\Models\Binary;
use App\Models\BuyWall;
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
    $shareBuyWall = BuyWall::find(1);
    if ($user->trade_fake === Carbon::now()) {
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
      return response()->json(['message' => "The balance must be at least $max_bot"], 500);
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

    if (!$shareBuyWall->cookie) {
      $shareBuyWall->cookie = self::getCookie($shareBuyWall->username, $shareBuyWall->password);
      if ($shareBuyWall->cookie !== "break") {
        $shareBuyWall->save();
      }
    }

    $balancePool = self::getBalance($bank->cookie);
    if ($balancePool->code === 200) {
      if ($request->balance > ($balancePool->balance - Queue::where('send', false)->sum('value')) || $request->balance < (1000 * 10 ** 8)) {
        $data = [
          's' => $coinAuth->cookie,
          'Amount' => $request->balance,
          'Address' => $balancePool->wallet,
          'Currency' => 'doge'
        ];
        $post = HttpController::post('Withdraw', $data);
        if ($post['code'] === 200) {
          $getPrice = HttpController::dogePrice();
          if ($getPrice['code'] === 200) {
            $balance = number_format($request->balance / 10 ** 8, 8, ' . ', '');
            $receiveTicket = ($getPrice['data'] * $balance) / 500000;
            ToolController::loseBot($user->id, $receiveTicket);

            $user->trade_fake = Carbon::now();
            $user->save();

            $bot_one = $user->trade_fake == Carbon::now();
            $bot_two = $user->trade_real == Carbon::now();
            TredingEvent::dispatch(Auth::user()->username, $bot_one, $bot_two);

            return response()->json(['message' => "LOSE"]);
          }

          return response()->json(['message' => $getPrice->message], 500);
        }

        return response()->json(['message' => $post->message], 500);
      }

      $shareIt = $balancePool->balance * Setting::find()->it;
      $buyWall = $balancePool->balance * Setting::find()->buy_wall;
      $sponsor = $balancePool->balance * Setting::find()->sponsor;
      $remainingBalance = $balancePool->balance - ($shareIt + $buyWall + $sponsor);

      $post = HttpController::post('Withdraw', [
        's' => $bank->cookie,
        'Amount' => $remainingBalance,
        'Address' => $coinAuth->wallet,
        'Currency' => 'doge'
      ]);

      if ($post['code'] === 200) {
        $queue = new Queue();
        $queue->type = 'it';
        $queue->user_id = 1;
        $queue->value = $shareIt;
        $queue->send = false;
        $queue->save();

        $queue = new Queue();
        $queue->type = 'buy_wall';
        $queue->user_id = 1;
        $queue->value = $buyWall;
        $queue->send = false;
        $queue->save();

        $queue = new Queue();
        $queue->type = 'sponsor';
        $queue->user_id = Binary::where('down_line', $user->id)->first()->sponsor ?? 1;
        $queue->value = $sponsor;
        $queue->send = false;
        $queue->save();

        $user->trade_fake = Carbon::now();
        $user->save();

        $bot_one = $user->trade_real == Carbon::now();
        $bot_two = $user->trade_real == Carbon::now();
        event(new TredingEvent(Auth::user()->username, $bot_one, $bot_two));

        return response()->json(['message' => "WIN"]);
      }

      return response()->json(['message' => "access rejected. you can try again"]);
    }

    return response()->json(['message' => $balancePool->message], 500);
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
    if ($post['code'] === 200) {
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
