<?php

namespace App\Http\Controllers\API;

use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  public function login(Request $request)
  {
    if (filter_var($request->username, FILTER_VALIDATE_EMAIL)) {
      $type = 'email';
    } else if (filter_var($request->username, FILTER_VALIDATE_INT)) {
      $type = 'phone';
    } else {
      $type = 'username';
    }
    Logger::info($request->username . " attempt to login from (" . $request->ip() . ")");
    $request->validate([
      "username" => "required|string|in:users,$type",
      "password" => "required"
    ]);
    try {
      if (Auth::attempt([$type => $request->username, 'password' => $request->password])) {
        Logger::info($request->username . " successful login from (" . $request->ip() . ")");
        foreach (Auth::user()->tokens as $id => $item) {
          $item->delete();
        }
        if ($user = Auth::user()) {
          Logger::info($request->username . " Successfully Login but return invalid user");
          // TODO: Additional Login value
          response()->json([
            "code" => 200,
            "user" => [
              "username" => $user->username,
              "email" => $user->email,
              "phone" => $user->phone,
              "tradeFake" => $user->trade_fake,
              "tradeReal" => $user->trade_real,
              "isSuspended" => $user->suspend,
            ]
          ]);
        } else {
          Logger::error($request->username . " Successfully Login but return invalid user");
          return response()->json(['code' => 500, 'message' => 'Invalid user'], 500);
        }
      } else {
        Logger::info($request->username . " failed login attempt from (" . $request->ip() . ")");
        response()->json(["code" => 400, "message" => "Username and/or Password didn't match"], 400);
      }
    } catch (Exception $e) {
      Logger::error('[' . $e->getCode() . '] "' . $e->getMessage() . '" on line ' . $e->getTrace()[0]['line'] . ' of file ' . $e->getTrace()[0]['file']);
      response()->json(['code' => 500, "message" => "Something happen at our end"]);
    }
  }

  public function register(Request $request)
  {
    Logger::info("Register attempt from (" . $request->ip() . ")");
    $request->validate([
      "username" => "required|unique:users,username",
      "email" => "required|unique:users,email",
      "phone" => "required|regex:/^(0|\+62)(2|8)\d{5,13}/",
      "password" => "required|same:confirmation_password|min:6"
    ]);
    try {
      $user = new User([
        "username" => $request->username,
        "email" => $request->email,
        "phone" => $request->phone,
        "password" => Hash::make($request->password)
      ]);
      // TODO: Additional user field
      $user->save();
    } catch (Exception $e) {
      Logger::error('[' . $e->getCode() . '] "' . $e->getMessage() . '" on line ' . $e->getTrace()[0]['line'] . ' of file ' . $e->getTrace()[0]['file']);
      response()->json(['code' => 500, "message" => "Something happen at our end"]);
    }
  }
}
