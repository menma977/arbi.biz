<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Setting;

class InfoController extends Controller
{
  public function __invoke()
  {
    $info = Setting::select([
      'maintenance',
      'version',
      'min_bot',
      'max_bot',
      'it',
      'buy_wall',
      'sponsor',
    ])->first();
    $info->wallet_bank = Bank::first()->wallet;
    return response()->json($info);
  }
}
