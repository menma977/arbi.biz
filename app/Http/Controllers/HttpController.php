<?php

namespace App\Http\Controllers;

use App\Models\ListUrl;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class HttpController
{
  static private $key = "";

  /**
   * @param $action
   * @param $body
   * @param bool $needKey
   * @return Collection
   */
  public static function post($action, $body, $needKey = true)
  {
    $url = ListUrl::where("block", false)->first();
    if (!$url) {
      return collect(['code' => 400, 'message' => 'blocked']);
    }
    if ($needKey) {
      $body["Key"] = self::$key;
    }
    $body["a"] = $action;
    $post = Http::asForm()->withHeaders([
      'referer' => 'https://arbi.biz/',
      'origin' => 'https://arbi.biz/'
    ])->post($url, $body);

    $data = new Collection();

    switch ($post) {
      case $post->serverError():
        $data->push('code', 500);
        $data->push('message', 'server error code 500');
        $data->push('data', []);
        break;
      case $post->clientError():
        $data->push('code', 401);
        $data->push('message', 'client error code 401');
        $data->push('data', []);
        break;
      case str_contains($post->body(), 'IP are blocked for 2 minutes.') === true:
        $url->block = true;
        $url->start_at = Carbon::now();
        $url->save();
        $data->push('code', 500);
        $data->push('message', 'server has been blocked');
        $data->push('data', []);
        break;
      case str_contains($post->body(), 'ChanceTooHigh') === true:
        $data->push('code', 400);
        $data->push('message', 'Chance Too High');
        $data->push('data', []);
        break;
      case str_contains($post->body(), 'ChanceTooLow') === true:
        $data->push('code', 400);
        $data->push('message', 'Chance Too Low');
        $data->push('data', []);
        break;
      case str_contains($post->body(), 'InsufficientFunds') === true:
        $data->push('code', 400);
        $data->push('message', 'Insufficient Funds');
        $data->push('data', []);
        break;
      case str_contains($post->body(), 'NoPossibleProfit') === true:
        $data->push('code', 400);
        $data->push('message', 'No Possible Profit');
        $data->push('data', []);
        break;
      case str_contains($post->body(), 'MaxPayoutExceeded') === true:
        $data->push('code', 400);
        $data->push('message', 'Max Payout Exceeded');
        $data->push('data', []);
        break;
      case str_contains($post->body(), '999doge') === true:
        $data->push('code', 400);
        $data->push('message', 'Invalid request On Server Wait 5 minute to try again');
        $data->push('data', []);
        break;
      case str_contains($post->body(), 'error') === true:
        $data->push('code', 400);
        $data->push('message', 'Invalid request');
        $data->push('data', []);
        break;
      case str_contains($post->body(), 'TooFast') === true:
        $data->push('code', 400);
        $data->push('message', 'Too Fast');
        $data->push('data', []);
        break;
      case str_contains($post->body(), 'TooSmall') === true:
        $data->push('code', 400);
        $data->push('message', 'Too Small');
        $data->push('data', []);
        break;
      case str_contains($post->body(), 'LoginRequired') === true:
        $data->push('code', 400);
        $data->push('message', 'Login Required');
        $data->push('data', []);
        break;
      case str_contains($post->body(), 'InvalidApiKey') === true:
        $data->push('code', 400);
        $data->push('message', 'key you provided is invalid');
        $data->push('data', []);
        break;
      default:
        $data->push('code', 200);
        $data->push('message', 'successful');
        $data->push('data', collect($post->json()));
        break;
    }

    return $data;
  }

  /**
   * @param $wallet
   * @return Collection
   */
  public static function checkWallet($wallet)
  {
    $get = Http::get("https://sochain.com/api/v2/is_address_valid/DOGE/$wallet");
    $data = new Collection();

    if ($get->serverError()) {
      $data->push('code', 500);
      $data->push('message', 'server error code 500');
      $data->push('data', []);
    } else if ($get->clientError()) {
      $data->push('code', 401);
      $data->push('message', 'client error code 401');
      $data->push('data', []);
    } else if ($get["status"] === "success") {
      if ($get["data"]["is_valid"] === true) {
        $data->push('code', 200);
        $data->push('message', 'valid wallet');
        $data->push('data', $get["data"]["address"]);
      } else {
        $data->push('code', 401);
        $data->push('message', 'Invalid wallet');
        $data->push('data', $get["data"]["address"]);
      }
    } else {
      $data->push('code', 500);
      $data->push('message', 'wallet required');
      $data->push('data', []);
    }

    return $data;
  }

  /**
   * @return Collection
   */
  public static function dogePrice()
  {
    $get = Http::get("https://indodax.com/api/ticker/dogeidr");
    $data = new Collection();

    if ($get->serverError()) {
      $data->push('code', 500);
      $data->push('message', 'server error code 500');
      $data->push('data', 0);
    } else if ($get->clientError()) {
      $data->push('code', 401);
      $data->push('message', 'client error code 401');
      $data->push('data', 0);
    } else {
      $data->push('code', 200);
      $data->push('message', 'successful');
      $data->push('data', $get["ticker"]["buy"]);
    }

    return $data;
  }
}
