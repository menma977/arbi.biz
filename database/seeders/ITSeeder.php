<?php

namespace Database\Seeders;

use App\Models\IT;
use Illuminate\Database\Seeder;

class ITSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $it = new IT();
    $it->wallet = "DH2RsGMm4dN7napXN5KKxpNSjvHhyTA9GD";
    $it->save();
  }
}
