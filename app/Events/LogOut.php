<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LogOut implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $username;

  /**
   * Create a new event instance.
   *
   * @param $username
   * @param $ticket
   */
  public function __construct($username)
  {
    $this->username = $username;
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return Channel|array
   */
  public function broadcastOn()
  {
    return new PrivateChannel("arbi.biz.{$this->username}");
  }
}
