<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Announcement implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $title;
  public $message;
  public $type;

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
    Log::debug('announce');
    return new Channel('arbi.biz.announcement');
  }
}
