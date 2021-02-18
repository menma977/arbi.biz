<?php

namespace App\Http\Controllers\API\Bot;

use App\Events\TredingEvent;
use App\Http\Controllers\Controller;
use App\Models\Binary;
use App\Models\HistoryBot;
use App\Models\Queue;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MartiAngelController extends Controller
{
  /**
   * @param Request $request
   * @return JsonResponse
   * @throws ValidationException
   */
  public function index(Request $request): JsonResponse
  {
    $user = Auth::user();
    $this->validate($request, [
      'start_balance' => 'required|numeric',
      'end_balance' => 'required|numeric',
      'target_balance' => 'required|numeric',
      'pay_in' => 'required|numeric',
      'pay_out' => 'required|numeric',
      'low' => 'required|numeric',
      'high' => 'required|numeric',
      'status' => 'required|string',
      'is_finish' => 'required',
    ]);

    $historyBot = new HistoryBot();
    $historyBot->user_id = Auth::id();
    $historyBot->start_balance = $request->start_balance;
    $historyBot->end_balance = $request->end_balance;
    $historyBot->target_balance = $request->target_balance;
    $historyBot->bot = "MartiAngel";
    $historyBot->pay_in = $request->pay_in;
    $historyBot->pay_out = $request->pay_out;
    $historyBot->low = $request->low;
    $historyBot->high = $request->high;
    $historyBot->status = $request->status;
    if ($historyBot->start_balance >= $historyBot->target_balance) {
      $historyBot->is_finish = true;
      $user->trade_real = Carbon::now();
      $user->save();
    } else {
      $historyBot->is_finish = false;
    }
    $historyBot->save();

    $bot_one = $user->trade_fake == Carbon::now();
    $bot_two = $user->trade_real == Carbon::now();
    TredingEvent::dispatch(Auth::user()->username, $bot_one, $bot_two);

    $data = [
      'start_balance' => $historyBot->start_balance,
      'end_balance' => $historyBot->end_balance,
      'target_balance' => $historyBot->target_balance,
      'pay_in' => $historyBot->pay_in,
      'pay_out' => $historyBot->pay_out,
      'low' => $historyBot->low,
      'high' => $historyBot->high,
      'status' => $historyBot->status,
      'is_finish' => $historyBot->is_finish,
    ];

    return response()->json($data);
  }

  /**
   * @param $balance
   * @return JsonResponse
   */
  public function store($balance)
  {
    $shareIt = $balance * Setting::first()->it;
    $buyWall = $balance * Setting::first()->buy_wall;
    $sponsor = $balance * Setting::first()->sponsor;

    $queueBuyWall = new Queue();
    $queueBuyWall->type = 'buy_wall';
    $queueBuyWall->user_id = 1;
    $queueBuyWall->value = $buyWall + $shareIt;
    $queueBuyWall->send = false;
    $queueBuyWall->save();

    $queueSponsor = new Queue();
    $queueSponsor->type = 'sponsor';
    $queueSponsor->user_id = Binary::where('down_line', Auth::id())->first()->sponsor ?? 1;
    $queueSponsor->value = $sponsor;
    $queueSponsor->send = false;
    $queueSponsor->save();

    return response()->json(["message" => "success"]);
  }
}
