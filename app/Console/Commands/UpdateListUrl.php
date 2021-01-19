<?php

namespace App\Console\Commands;

use App\Models\ListUrl;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateListUrl extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'update_list_url';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Update list url when when block more then 10 minute';

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    $url = ListUrl::where('block', true)->where('updated_at', '<=', Carbon::now()->addMonths(-1))->first();
    if ($url) {
      $url->block = false;
      $url->save();
    }
  }
}
