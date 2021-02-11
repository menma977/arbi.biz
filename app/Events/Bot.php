<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Bot
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  protected $min;
  protected $max;
  protected $it;
  protected $sponsor;
  protected $buy_wall;

  /**
   * Create a new event instance.
   *
   * @param $min
   * @param $max
   * @param $it
   * @param $sponsor
   * @param $buy_wall
   */
  public function __construct($min, $max, $it, $sponsor, $buy_wall)
  {
    $this->min = $min;
    $this->max = $max;
    $this->it = $it;
    $this->sponsor = $sponsor;
    $this->buy_wall = $buy_wall;
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return Channel|array
   */
  public function broadcastOn()
  {
    return new Channel("arbi.biz.bot");
  }
}
