<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BuyWall;
use App\Models\IT;
use App\Models\Setting;
use Illuminate\Http\Request;

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
    $info->it_wallet = IT::first()->wallet;
    $info->buy_wall_wallet = BuyWall::first()->wallet;
    return response()->json($info);
  }
}
