<?php

namespace App\Http\Controllers;

use App\Models\Setting;

class InfoController extends Controller
{
  public function __invoke()
  {
    return response()->json(Setting::select([
      'maintenance',
      'version',
      'min_bot',
      'max_bot',
      'it',
      'buy_wall',
      'sponsor',
    ])->first());
  }
}
