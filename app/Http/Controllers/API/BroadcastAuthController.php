<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BroadcastAuthController extends Controller
{
  /**
   * @param Request $request
   * @return JsonResponse
   */
  public function __invoke(Request $request): JsonResponse
  {
    return response()->json([
      "auth" => env("PUSHER_APP_KEY") . ":" . hash_hmac("SHA256", $request->post("socket_id") . ":" . $request->post("channel_name"), env("PUSHER_APP_SECRET"))
    ]);
  }
}
