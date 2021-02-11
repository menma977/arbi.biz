<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    $this->call(UserSeeder::class);
    $this->call(BinarySeeder::class);
    $this->call(ListUrlSeeder::class);
    $this->call(SettingSeeder::class);
    $this->call(TicketSeeder::class);

    $this->call(BuyWallSeeder::class);
    $this->call(ITSeeder::class);
  }
}
