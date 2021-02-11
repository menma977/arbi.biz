<?php

namespace App\Http\Controllers;

use App\Events\TicketEvent;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TicketController extends Controller
{
  public function index()
  {
    $users = User::all();
    $list = Ticket::orderBy('created_at', 'desc')->simplePaginate(20);
    $list->getCollection()->transform(function ($item) {
      $item->user = User::find($item->user_id);
      $item->date = Carbon::parse($item->created_at)->format('H:i:s d/M/Y');

      return $item;
    });

    $data = [
      'users' => $users,
      'list' => $list
    ];

    return view("ticket.index", $data);
  }

  /**
   * @param Request $request
   * @return RedirectResponse
   * @throws ValidationException
   */
  public function store(Request $request): RedirectResponse
  {
    $this->validate($request, [
      'username' => 'required|exists:users,id',
      'total' => 'required|numeric|min:0.00000001'
    ]);

    $user = User::find($request->input('username'));

    $ticket = new Ticket();
    $ticket->user_id = $user->id;
    $ticket->debit = $request->input('total');
    $ticket->description = "Admin Add Ticket";
    $ticket->save();

    self::pushEvent($user);

    return redirect()->back()->with(['message' => "Success add ticket on {$user->username}"]);
  }

  /**
   * @param Request $request
   * @return RedirectResponse
   * @throws ValidationException
   */
  public function update(Request $request): RedirectResponse
  {
    $this->validate($request, [
      'username' => 'required|exists:users,id',
      'total' => 'required|numeric|min:0.00000001'
    ]);

    $user = User::find($request->input('username'));

    $ticket = new Ticket();
    $ticket->user_id = $user->id;
    $ticket->credit = $request->input('total');
    $ticket->description = "Admin remove Ticket";
    $ticket->save();

    self::pushEvent($user);

    return redirect()->back()->with(['message' => "Success remove ticket on {$user->username}"]);
  }

  /**
   * @param $user
   */
  private static function pushEvent($user): void
  {
    event(new TicketEvent($user->username, self::totalTicket($user->id)));
  }

  /**
   * @param $user_id
   * @return float
   */
  private static function totalTicket($user_id): float
  {
    return Ticket::where('user_id', $user_id)->sum('debit') - Ticket::where('user_id', $user_id)->sum('credit');
  }
}
