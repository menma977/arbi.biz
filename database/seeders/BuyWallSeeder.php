<?php

namespace Database\Seeders;

use App\Models\BuyWall;
use Illuminate\Database\Seeder;

class BuyWallSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $buy_wall = new BuyWall();
    $buy_wall->wallet = "DH2RsGMm4dN7napXN5KKxpNSjvHhyTA9GD";
    $buy_wall->save();
  }
}
