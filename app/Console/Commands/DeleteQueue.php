<?php

namespace App\Console\Commands;

use App\Models\Queue;
use Illuminate\Console\Command;

class DeleteQueue extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'delete_queue';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Delete queue';

  /**
   * Execute the console command.
   *
   */
  public function handle(): void
  {
    Queue::where("send", true)->delete();
  }
}
