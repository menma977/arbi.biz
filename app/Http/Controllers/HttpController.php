<?php

namespace App\Http\Controllers;

use App\Models\ListUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HttpController extends Controller
{
  public function post($body)
  {
    $url = ListUrl::where("block", false)->first();
    if (!$url) return false;
    $post = Http::asForm()->withHeaders([
      'referer' => 'https://arbi.biz/',
      'origin' => 'https://arbi.biz/'
    ])->post($url, $body);

    switch ($post) {
      case $post->serverError():
        return response()->json(['code' => 500, 'message' => 'server error code 500'], 500);
      case $post->clientError():
        return response()->json(['code' => 401, 'message' => 'client error code 401'], 401);
      case str_contains($post->body(), 'IP are blocked for 2 minutes.') === true:
        return response()->json(['code' => 500, 'message' => 'server has been blocked'], 400);
      case str_contains($post->body(), 'ChanceTooHigh') === true:
        return response()->json(['code' => 400, 'message' => 'Chance Too High'], 400);
      case str_contains($post->body(), 'ChanceTooLow') === true:
        return response()->json(['code' => 400, 'message' => 'Chance Too Low'], 400);
      case str_contains($post->body(), 'InsufficientFunds') === true:
        return response()->json(['code' => 400, 'message' => 'Insufficient Funds'], 400);
      case str_contains($post->body(), 'NoPossibleProfit') === true:
        return response()->json(['code' => 400, 'message' => 'No Possible Profit'], 400);
      case str_contains($post->body(), 'MaxPayoutExceeded') === true:
        return response()->json(['code' => 400, 'message' => 'Max Payout Exceeded'], 400);
      case str_contains($post->body(), '999doge') === true:
        return response()->json(['code' => 400, 'message' => 'Invalid request On Server Wait 5 minute to try again'], 400);
      case str_contains($post->body(), 'error') === true:
        return response()->json(['code' => 400, 'message' => 'Invalid request'], 400);
      case str_contains($post->body(), 'TooFast') === true:
        return response()->json(['code' => 400, 'message' => 'Too Fast'], 400);
      case str_contains($post->body(), 'TooSmall') === true:
        return response()->json(['code' => 400, 'message' => 'Too Small'], 400);
      case str_contains($post->body(), 'LoginRequired') === true:
        return response()->json(['code' => 400, 'message' => 'Login Required'], 400);
      case str_contains($post->body(), 'InvalidApiKey') === true:
        return response()->json(['code' => 400, 'message' => 'key you provided is invalid'], 400);
      default:
        return response()->json(['code' => 200, 'data' => $post->json()]);
    }
  }
}
