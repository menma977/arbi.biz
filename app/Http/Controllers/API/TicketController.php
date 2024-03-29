<?php

namespace App\Http\Controllers\API;

use App\Events\TicketEvent;
use App\Http\Controllers\Controller;
use App\Models\CoinAuth;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TicketController extends Controller
{
  protected $ticket;

  /**
   * @return JsonResponse
   */
  public function index(): JsonResponse
  {
    $this->ticket = Ticket::where("user_id", Auth::id())->sum("debit") - Ticket::where("user_id", Auth::id())->sum("credit");

    $data = [
      "ticket" => $this->ticket,
    ];

    return response()->json($data);
  }

  /**
   * @param Request $request
   * @return JsonResponse
   * @throws ValidationException
   */
  public function store(Request $request): JsonResponse
  {
    $this->ticket = Ticket::where("user_id", Auth::id())->sum("debit") - Ticket::where("user_id", Auth::id())->sum("credit");
    $this->validate($request, [
      "total" => "required|numeric|min:0.00000001|max:$this->ticket",
      "wallet" => "required|exists:coin_auths,wallet"
    ]);

    $coinUser = CoinAuth::where("wallet", $request->input("wallet"))->first();
    $user = User::where("id", $coinUser->user_id)->first();

    $ticketDebit = new Ticket();
    $ticketDebit->description = $user->username . " have received " . $request->input("total") . " ticket from " . Auth::user()->username;
    $ticketDebit->user_id = $coinUser->user_id;
    $ticketDebit->debit = $request->input("total");
    $ticketDebit->save();

    $ticketDebit = new Ticket();
    $ticketDebit->description = Auth::user()->username . " have given " . $request->input("total") . " ticket to " . $user->username;
    $ticketDebit->user_id = Auth::id();
    $ticketDebit->credit = $request->input("total");
    $ticketDebit->save();

    event(new TicketEvent($user->username, self::totalTicket($coinUser->user_id)));
    event(new TicketEvent(Auth::user()->username, self::totalTicket(Auth::user()->username)));

    return response()->json(["message" => "ticket has been send"]);
  }

  /**
   * @param $user_id
   * @return int
   */
  private static function totalTicket($user_id): int
  {
    return Ticket::where("user_id", $user_id)->sum("debit") - Ticket::where("user_id", $user_id)->sum("credit");
  }
}
