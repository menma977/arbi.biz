<?php

namespace App\Http\Controllers;

use App\Models\HistoryBot;
use App\Models\HistoryPin;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;

class ToolController
{
  /**
   * @param $user_id
   * @param $value
   * @param $username
   */
  public static function register($user_id, $value, $username)
  {
    $user = User::find($user_id);

    $ticket = new Ticket();
    $ticket->user_id = $user->id;
    $ticket->description = "register new user with username : $username";
    $ticket->credit = $value;
    $ticket->save();

    $historyTicket = new HistoryPin();
    $historyTicket->user_id = $user->id;
    $historyTicket->description = "register new user with username : $username";
    $historyTicket->value = $value;
    $historyTicket->save();
  }

  /**
   * @param $user_id
   * @param $value
   */
  public static function loseBot($user_id, $value)
  {
    $ticket = new Ticket();
    $ticket->user_id = $user_id;
    $ticket->description = "compensation for losing the bot";
    $ticket->debit = $value;
    $ticket->save();

    $historyTicket = new HistoryPin();
    $historyTicket->user_id = $user_id;
    $historyTicket->description = "compensation for losing the bot";
    $historyTicket->value = $value;
    $historyTicket->save();
  }

  /**
   * @param $user_id
   * @param $target_id
   * @param $value
   */
  public static function sendPin($user_id, $target_id, $value)
  {
    $user = User::find($user_id);
    $target = User::find($target_id);

    $ticket = new Ticket();
    $ticket->user_id = $user->id;
    $ticket->description = "send $value ticket to {$target->username}";
    $ticket->credit = $value;
    $ticket->save();

    $ticket = new Ticket();
    $ticket->user_id = $target->id;
    $ticket->description = "receive $value ticket to {$user->username}";
    $ticket->debit = $value;
    $ticket->save();

    $historyTicket = new HistoryPin();
    $historyTicket->user_id = $user->id;
    $historyTicket->description = "send $value ticket to {$target->username}";
    $historyTicket->value = $value;
    $historyTicket->save();

    $historyTicket = new HistoryPin();
    $historyTicket->user_id = $target->id;
    $historyTicket->description = "send $value ticket to {$user->username}";
    $historyTicket->value = $value;
    $historyTicket->save();
  }

  /**
   * @param $user_id
   * @param $bot
   * @param $pay_in
   * @param $pay_out
   * @param $low
   * @param $high
   * @param $win
   */
  public static function historyBot($user_id, $bot, $pay_in, $pay_out, $low, $high, $win)
  {
    $user = User::find($user_id);
    if ($bot === 1) {
      $user->trade_fake = Carbon::now();
    } else {
      $user->trade_real = Carbon::now();
    }

    $historyBot = new HistoryBot();
    $historyBot->user_id = $user->id;
    $historyBot->bot = $bot;
    $historyBot->pay_in = $pay_in;
    $historyBot->pay_out = $pay_out;
    $historyBot->low = $low;
    $historyBot->high = $high;
    $historyBot->status = $win === true ? "WIN" : "LOSE";

    $historyBot->save();
    $user->save();
  }
}
