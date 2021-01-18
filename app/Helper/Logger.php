<?php

namespace App\Helper;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;
/* @method static void alert(string $message, array $context = [])
 * @method static void critical(string $message, array $context = [])
 * @method static void debug(string $message, array $context = [])
 * @method static void emergency(string $message, array $context = [])
 * @method static void error(string $message, array $context = [])
 * @method static void info(string $message, array $context = [])
 * @method static void log($level, string $message, array $context = [])
 * @method static void notice(string $message, array $context = [])
 * @method static void warning(string $message, array $context = [])
 * @method static void write(string $level, string $message, array $context = [])
 */

class Logger extends Log
{
  static function warning(string $message, array $context = [])
  {
    if (Setting::find(1)->logging <= 1)
      Log::warning($message, $context);
  }

  static function info(string $message, array $context = [])
  {
    if (Setting::find(1)->logging <= 2)
      Log::info($message, $context);
  }
}
