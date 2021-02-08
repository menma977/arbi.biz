<?php

namespace App\Http\Controllers\API\Bot;

use App\Events\TredingEvent;
use App\Http\Controllers\Controller;
use App\Models\HistoryBot;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MartiAngelController extends Controller
{
  protected $user;

  /**
   * FakeController constructor.
   */
  public function __construct()
  {
    $this->user = Auth::user();
  }

  /**
   * @param Request $request
   * @return JsonResponse
   * @throws ValidationException
   */
  public function index(Request $request): JsonResponse
  {
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
      $this->user->trade_real = Carbon::now();
      $this->user->save();
    } else {
      $historyBot->is_finish = false;
    }
    $historyBot->save();

    $bot_one = $this->user->trade_fake == Carbon::now();
    $bot_two = $this->user->trade_real == Carbon::now();
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
}
