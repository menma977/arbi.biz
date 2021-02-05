<?php

namespace App\Http\Controllers;

use App\Events\TicketEvent;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TicketController extends Controller
{
  public function index()
  {

  }

  public function create()
  {

  }

  /**
   * @param Request $request
   * @return RedirectResponse
   * @throws ValidationException
   */
  public function store(Request $request): RedirectResponse
  {
    $this->validate($request, [
      'username' => 'required|exists:users,username',
      'total' => 'required|numeric|min:0.00000001'
    ]);

    $user = User::where('username', $request->input('username'))->first();

    $ticket = new Ticket();
    $ticket->user_id = $user->id;
    $ticket->debit = $request->input('total');
    $ticket->description = "Admin Add Ticket";
    $ticket->save();

    $totalTicket = Ticket::where('user_id', $user->id)->sum('debit') - Ticket::where('user_id', $user->id)->sum('credit');

    event(new TicketEvent($user->username, $totalTicket));

    return redirect()->back()->with(['message' => "Success add ticket on {$user->username}"]);
  }

  public function edit()
  {

  }

  public function update()
  {

  }
}
