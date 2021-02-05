<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TredingEvent
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  protected $username;
  protected $bot_one;
  protected $bot_two;

  /**
   * Create a new event instance.
   *
   * @param $username
   * @param bool $bot_one
   * @param bool $bot_two
   */
  public function __construct($username, $bot_one = false, $bot_two = false)
  {
    $this->username = $username;
    $this->bot_one = $bot_one;
    $this->bot_two = $bot_two;
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
