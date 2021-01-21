<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('settings', function (Blueprint $table) {
      $table->id();
      $table->boolean("maintenance")->default(false);
      $table->integer("logging")->default(1);
      $table->integer("version")->default(1);
      $table->bigInteger('min_bot')->default(300000000000);
      $table->bigInteger('max_bot')->default(1000000000000);
      $table->double("it")->default(0.01);
      $table->double("buy_wall")->default(0.01);
      $table->double("sponsor")->default(0.01);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('settings');
  }
}
