<?php

namespace App\Http\Controllers\API\Auth;

use App\Helper\Logger;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
  /**
   * @param Request $request
   * @return Application|ResponseFactory|Response
   */
  public function __invoke(Request $request)
  {
    Logger::warning("Logout: attempt from " . Auth::user()->username);
    foreach (Auth::user()->tokens as $key => $value) {
      $value->delete();
    }
    return response('', 204);
  }
}
