<?php

namespace App\Http\Controllers;

use App\Models\HistoryBot;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Phpml\Classification\KNearestNeighbors;

class DashboardController extends Controller
{
  /**
   * @return Application|Factory|View
   */
  public function index()
  {
    $balance = random_int(1000000000, 99999999999);
    $random = random_int(10000000, 99999999);
    dump($balance);
    dump($random);
    dd($this->predict($balance, $random));
    $historyBot = HistoryBot::whereDate('created_at', Carbon::now())->latest()->take(40)->get()->map(function ($item) {
      $item->profit = (float)number_format(($item->pay_out - $item->pay_in) / 10 ** 8, 5, ".", "");

      return $item;
    })->pluck('profit');

    $data = [
      'historyBot' => $historyBot
    ];

    return view('dashboard', $data);
  }

  private function predict($balance, $payIn)
  {
    $data = HistoryBot::select("start_balance", "pay_in")->get();
    $dataSet = [];
    $index = 0;
    foreach ($data as $item) {
      $dataSet[$index] = [$item->start_balance, $item->pay_in];
      $index++;
    }
    $status = HistoryBot::all()->pluck('status')->toArray();
    $classifier = new KNearestNeighbors();
    $classifier->train($dataSet, $status);

    return $classifier->predict([$balance, $payIn]);
  }
}
