<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Announcement implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  protected $title;
  protected $message;
  protected $type;

  /**
   * Create a new event instance.
   *
   * @param $title
   * @param $message
   * @param string $type
   */
  public function __construct($title, $message, $type = "info")
  {
    $this->title = $title;
    $this->message = $message;
    $this->type = $type;
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return Channel|array
   */
  public function broadcastOn()
  {
    return new Channel('arbi.biz.announcement');
  }
}
