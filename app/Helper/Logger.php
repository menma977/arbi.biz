<?php

namespace App\Helper;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class Logger
{

  static function error(string $message, array $context = [])
  {
    if (Setting::find(1)->logging <= 1)
      Log::error($message, $context);
  }

  static function warning(string $message, array $context = [])
  {
    if (Setting::find(1)->logging <= 2)
      Log::warning($message, $context);
  }

  static function info(string $message, array $context = [])
  {
    if (Setting::find(1)->logging <= 3)
      Log::info($message, $context);
  }
}
