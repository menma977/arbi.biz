<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TicketEvent implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $username;
  public $ticket;

  /**
   * Create a new event instance.
   *
   * @param $username
   * @param $ticket
   */
  public function __construct($username, $ticket)
  {
    $this->username = $username;
    $this->ticket = $ticket;
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return Channel|array
   */
  public function broadcastOn()
  {
    Log::debug('TICKET EVENT ' . "arbi.biz.{$this->username}");
    Log::debug($this->username);
    Log::debug($this->ticket);
    return new PrivateChannel("private-arbi.biz.{$this->username}");
  }
}
