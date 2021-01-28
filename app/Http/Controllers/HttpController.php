<?php

namespace App\Http\Controllers;

use App\Models\ListUrl;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class HttpController
{
  static private $key = "12650d1e50194d789bf03d22f90ecebe";

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
    $post = Http::asForm()->withHeaders($data = [
      'referer' => 'https://arbi.biz/',
      'origin' => 'https://arbi.biz/'
    ])->post($url->url, $body);

    switch ($post) {
      case $post->serverError():
        $data = [
          'code' => 500,
          'message' => 'server error code 500',
          'data' => [],
        ];
        break;
      case $post->clientError():
        $data = [
          'code' => 401,
          'message' => 'client error code 401',
          'data' => [],
        ];
        break;
      case str_contains($post->body(), 'IP are blocked for 2 minutes.') === true:
        $url->block = true;
        $url->start_at = Carbon::now();
        $url->save();
        $data = [
          'code' => 500,
          'message' => 'server has been blocked',
          'data' => [],
        ];
        break;
      case str_contains($post->body(), 'ChanceTooHigh') === true:
        $data = [
          'code' => 400,
          'message' => 'Chance Too High',
          'data' => [],
        ];
        break;
      case str_contains($post->body(), 'ChanceTooLow') === true:
        $data = [
          'code' => 400,
          'message' => 'Chance Too Low',
          'data' => [],
        ];
        break;
      case str_contains($post->body(), 'InsufficientFunds') === true:
        $data = [
          'code' => 400,
          'message' => 'Insufficient Funds',
          'data' => [],
        ];
        break;
      case str_contains($post->body(), 'NoPossibleProfit') === true:
        $data = [
          'code' => 400,
          'message' => 'No Possible Profit',
          'data' => [],
        ];
        break;
      case str_contains($post->body(), 'MaxPayoutExceeded') === true:
        $data = [
          'code' => 400,
          'message' => 'Max Payout Exceeded',
          'data' => [],
        ];
        break;
      case str_contains($post->body(), '999doge') === true:
        $data = [
          'code' => 400,
          'message' => 'Invalid request On Server Wait 5 minute to try again',
          'data' => [],
        ];
        break;
      case str_contains($post->body(), 'error') === true:
        $data = [
          'code' => 400,
          'message' => 'Invalid request',
          'data' => [],
        ];
        break;
      case str_contains($post->body(), 'TooFast') === true:
        $data = [
          'code' => 400,
          'message' => 'Too Fast',
          'data' => [],
        ];
        break;
      case str_contains($post->body(), 'TooSmall') === true:
        $data = [
          'code' => 400,
          'message' => 'Too Small',
          'data' => [],
        ];
        break;
      case str_contains($post->body(), 'LoginRequired') === true:
        $data = [
          'code' => 400,
          'message' => 'Login Required',
          'data' => [],
        ];
        break;
      case str_contains($post->body(), 'InvalidApiKey') === true:
        $data = [
          'code' => 400,
          'message' => 'key you provided is invalid',
          'data' => [],
        ];
        break;
      default:
        $data = [
          'code' => 200,
          'message' => 'successful',
          'data' => collect($post->json()),
        ];
        break;
    }

    return collect($data);
  }

  /**
   * @param $wallet
   * @return Collection
   */
  public static function checkWallet($wallet)
  {
    $get = Http::get("https://sochain.com/api/v2/is_address_valid/DOGE/$wallet");
    if ($get->serverError()) {
      $data = [
        "code" => 500,
        "message" => 'server error code 500',
        "data" => [],
      ];
    } else if ($get->clientError()) {
      $data = [
        "code" => 401,
        "message" => 'client error code 401',
        "data" => [],
      ];
    } else if ($get["status"] === "success") {
      if ($get["data"]["is_valid"] === true) {
        $data = [
          "code" => 200,
          "message" => 'valid wallet',
          "data" => $get["data"]["address"],
        ];
      } else {
        $data = [
          "code" => 401,
          "message" => 'Invalid wallet',
          "data" => $get["data"]["address"],
        ];
      }
    } else {
      $data = [
        "code" => 500,
        "message" => 'wallet required',
        "data" => [],
      ];
    }

    return collect($data);
  }

  /**
   * @return Collection
   */
  public static function dogePrice()
  {
    $get = Http::get("https://indodax.com/api/ticker/dogeidr");

    if ($get->serverError()) {
      $data = [
        "code" => 500,
        "message" => 'server error code 500',
        "data" => 0
      ];
    } else if ($get->clientError()) {
      $data = [
        "code" => 401,
        "message" => 'client error code 401',
        "data" => 0
      ];
    } else {
      $data = [
        "code" => 200,
        "message" => 'successful',
        "data" => $get["ticker"]["buy"],
      ];
    }

    return collect($data);
  }
}
