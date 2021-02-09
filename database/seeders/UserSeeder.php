<?php

namespace Database\Seeders;

use App\Models\Bank;
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

    $user->setUpdatedAt(Carbon::parse("last month"));
    $user->save();

    $coinAuth = new CoinAuth();
    $coinAuth->user_id = $user->id;
    $coinAuth->username = "arbi.biz";
    $coinAuth->password = "123456789";
    $coinAuth->wallet = "DET3qfAoK6jkMd5fyBdBvHmLfjJ1mVGqTr";
    $coinAuth->wallet_dax = "DDRXqgUdrmKW8xgtPMbM92HEAWBX3b867C";
    $coinAuth->save();

    $bank = new Bank();
    $bank->username = "arbi.bank";
    $bank->password = "123456789";
    $bank->wallet = "DDRXqgUdrmKW8xgtPMbM92HEAWBX3b867C";
    $bank->save();
  }
}
