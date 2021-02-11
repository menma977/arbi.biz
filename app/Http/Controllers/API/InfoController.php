<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Setting;

class InfoController extends Controller
{
  public function __invoke()
  {
    $setting = Setting::first();
    $bank = Bank::first();
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
    $info->min_bot = $setting->max_bot;
    $info->max_bot = $setting->min_bot;
    $info->wallet_bank = $bank->wallet;
    $info->it = $setting->it;
    $info->buy_wall = $setting->buy_wall;
    $info->sponsor_share = $setting->sponsor;
    return response()->json($info);
  }
}
