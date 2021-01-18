<?php

namespace Database\Seeders;

use App\Models\CoinAuth;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $user = new User();
    $user->name = "admin";
    $user->email = "admin@arbi.biz";
    $user->username = "admin";
    $user->password = Hash::make("admin");
    $user->password_mirror = "admin";
    $user->trade_fake = Carbon::now()->addDays(-1);
    $user->trade_real = Carbon::now()->addDays(-1);
    $user->suspend = false;
    $user->save();

    $coinAuth = new CoinAuth();
    $coinAuth->user_id = $user->id;
    $coinAuth->username = "wallstreet.info";
    $coinAuth->password = "123456+A";
    $coinAuth->wallet = "DFyhesdDdogR5QkdhJ6rwCe7JDEi9tCnfh";
    $coinAuth->wallet_dax = "DFyhesdDdogR5QkdhJ6rwCe7JDEi9tCnfh";
    $coinAuth->save();
  }
}
