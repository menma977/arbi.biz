<?php

namespace App\Helper;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class Logger
{
  public static function warning(string $message, array $context = [])
  {
    if (Setting::find(1)->logging <= 1) {
      Log::warning($message, $context);
    }
  }

  public static function info(string $message, array $context = [])
  {
    if (Setting::find(1)->logging <= 2) {
      Log::info($message, $context);
    }
  }

  public static function error(string $message, array $context = [])
  {
    if (Setting::find(1)->logging <= 2) {
      Log::error($message, $context);
    }
  }
}
