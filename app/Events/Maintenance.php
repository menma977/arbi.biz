<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Maintenance
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  protected $status;

  /**
   * Create a new event instance.
   *
   * @param $status
   */
  public function __construct($status)
  {
    $this->status = $status;
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return Channel|array
   */
  public function broadcastOn()
  {
    return new Channel("arbi.biz.maintenance");
  }
}
