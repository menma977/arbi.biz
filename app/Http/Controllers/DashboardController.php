<?php

namespace App\Http\Controllers;

use App\Models\HistoryBot;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
  /**
   * @return Application|Factory|View
   */
  public function index()
  {
    $historyBot = HistoryBot::whereDate('created_at', Carbon::now())->latest()->take(40)->get()->map(function ($item) {
      $item->profit = (float)number_format(($item->pay_out - $item->pay_in) / 10 ** 8, 5, ".", "");

      return $item;
    })->pluck('profit');

    $data = [
      'historyBot' => $historyBot
    ];

    return view('dashboard', $data);
  }
}
