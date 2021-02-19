<?php

namespace App\Console\Commands;

use App\Http\Controllers\HttpController;
use App\Models\Bank;
use App\Models\BuyWall;
use App\Models\Queue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class QueueExecution extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'queue_execution';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Withdraw balance from bank to wallet';

  /**
   * Execute the console command.
   */
  public function handle(): void
  {
    $queue = Queue::where('send', false)->where('created_at', '<=', Carbon::now())->first();
    if ($queue) {
      $bank = Bank::find(1);
      if (!$bank->cookie || $bank->updated_at <= Carbon::now()->addMonths(-1)) {
        $getCookie = $this->getCookie($bank->username, $bank->password);
        if ($getCookie !== "break") {
          $bank->cookie = $getCookie;
          $bank->save();
        }
      }

      $buy_wall = BuyWall::find(1);

      if ($queue->type === 'buy_wall') {
        $data = [
          's' => $bank->cookie,
          'Amount' => $queue->value,
          'Address' => $buy_wall->wallet,
          'Currency' => 'doge'
        ];
      } else {
        $data = [
          's' => $bank->cookie,
          'Amount' => $queue->value,
          'Address' => User::find($queue->user_id)->coinAuth->wallet,
          'Currency' => 'doge'
        ];
      }

      $post = HttpController::post("Withdraw", $data);

      if ($post['code'] === 200) {
        $queue->send = true;
      } else {
        $queue->created_at = Carbon::now()->addMinutes(5)->format('Y-m-d H:i:s');
      }
      $queue->save();
    }
  }

  /**
   * @param $username
   * @param $password
   * @return string
   */
  private function getCookie($username, $password): string
  {
    $data = [
      'username' => $username,
      'password' => $password,
    ];
    $post = HttpController::post("Login", $data, true);
    if ($post['code'] === 200) {
      return $post['data']['SessionCookie'];
    }
    return "break";
  }
}
