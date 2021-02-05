<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketEvent
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  protected $username;
  protected $ticket;

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
    return new PrivateChannel("arbi.biz.{$this->username}.ticket");
  }
}
