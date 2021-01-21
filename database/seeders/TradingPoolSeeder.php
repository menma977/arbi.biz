<?php

namespace Database\Seeders;

use App\Models\TradingPool;
use Illuminate\Database\Seeder;

class TradingPoolSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = new TradingPool();
    $data->username = "wallstreet.info";
    $data->password = "123456+A";
    $data->wallet = "DFyhesdDdogR5QkdhJ6rwCe7JDEi9tCnfh";
    $data->Save();
  }
}
