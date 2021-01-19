<?php

namespace App\Http\Controllers;

use App\Models\ListUrl;
use Illuminate\Support\Facades\Http;

class HttpController
{
  static private $key = "";

  /**
   * @param $action
   * @param $body
   * @param bool $needKey
   * @return array
   */
  public static function post($action, $body, $needKey = true)
  {
    $url = ListUrl::where("block", false)->first();
    if (!$url) {
      return ['code' => 400, 'message' => 'blocked'];
    }
    if ($needKey) {
      $body["Key"] = self::$key;
    }
    $body["a"] = $action;
    $post = Http::asForm()->withHeaders([
      'referer' => 'https://arbi.biz/',
      'origin' => 'https://arbi.biz/'
    ])->post($url, $body);

    switch ($post) {
      case $post->serverError():
        return ['code' => 500, 'message' => 'server error code 500'];
      case $post->clientError():
        return ['code' => 401, 'message' => 'client error code 401'];
      case str_contains($post->body(), 'IP are blocked for 2 minutes.') === true:
        return ['code' => 500, 'message' => 'server has been blocked'];
      case str_contains($post->body(), 'ChanceTooHigh') === true:
        return ['code' => 400, 'message' => 'Chance Too High'];
      case str_contains($post->body(), 'ChanceTooLow') === true:
        return ['code' => 400, 'message' => 'Chance Too Low'];
      case str_contains($post->body(), 'InsufficientFunds') === true:
        return ['code' => 400, 'message' => 'Insufficient Funds'];
      case str_contains($post->body(), 'NoPossibleProfit') === true:
        return ['code' => 400, 'message' => 'No Possible Profit'];
      case str_contains($post->body(), 'MaxPayoutExceeded') === true:
        return ['code' => 400, 'message' => 'Max Payout Exceeded'];
      case str_contains($post->body(), '999doge') === true:
        return ['code' => 400, 'message' => 'Invalid request On Server Wait 5 minute to try again'];
      case str_contains($post->body(), 'error') === true:
        return ['code' => 400, 'message' => 'Invalid request'];
      case str_contains($post->body(), 'TooFast') === true:
        return ['code' => 400, 'message' => 'Too Fast'];
      case str_contains($post->body(), 'TooSmall') === true:
        return ['code' => 400, 'message' => 'Too Small'];
      case str_contains($post->body(), 'LoginRequired') === true:
        return ['code' => 400, 'message' => 'Login Required'];
      case str_contains($post->body(), 'InvalidApiKey') === true:
        return ['code' => 400, 'message' => 'key you provided is invalid'];
      default:
        return ['code' => 200, 'data' => $post->json()];
    }
  }
}
