<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
  public function index()
  {
    $users = User::paginate(20);
    $users->getCollection()->transform(function ($item) {
      if ($item->trade_fake && Carbon::parse($item->trade_fake)->diffInDays(Carbon::now()) < 1) {
        $item->trade_fake = '<i class="icon text-success fas fa-check"></i>';
      } else {
        $item->trade_fake = '<i class="icon text-danger fas fa-ban"></i>';
      }

      if ($item->trade_real && Carbon::parse($item->trade_real)->diffInDays(Carbon::now()) < 1) {
        $item->trade_real = '<i class="icon text-success fas fa-check"></i>';
      } else {
        $item->trade_real = '<i class="icon text-danger fas fa-ban"></i>';
      }

      return $item;
    });

    $data = [
      "list" => $users
    ];

    return view("user.index", $data);
  }

  public function suspend($id)
  {
    $user = User::find($id);
    if ($user->suspend) {
      DB::table("oauth_access_tokens")->where("user_id", $user->id)->update(["revoked" => false]);
      $user->update(["suspend" => false]);
      return redirect()->back()->with(["message" => "$user->username has been unsuspended"]);
    }

    DB::table("oauth_access_tokens")->where("user_id", $user->id)->update(["revoked" => true]);
    $user->update(["suspend" => true]);
    return redirect()->back()->with(["message" => "$user->username has been suspended"]);
  }
}
