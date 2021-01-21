<?php

namespace Database\Seeders;

use App\Models\ListUrl;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ListUrlSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $l = new ListUrl();
    $l->url = "https://www.999doge.com/api/web.aspx";
    $l->block = false;
    $l->start_at = Carbon::yesterday();
    $l->save();
  }
}
