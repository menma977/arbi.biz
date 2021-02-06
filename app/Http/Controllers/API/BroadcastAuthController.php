<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BroadcastAuthController extends Controller
{
  function __invoke(Request $request)
  {
    return response()->json([
      "auth" => env("PUSHER_APP_KEY") . ":" . hash_hmac("SHA256", $request->post("socket_id") . ":" . $request->post("channel_name"), env("PUSHER_APP_SECRET"))
    ]);
  }
}
