<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

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
