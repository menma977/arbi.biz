<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryBotsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('history_bots', function (Blueprint $table) {
      $table->timestamps();
      $table->bigInteger('user_id')->index();
      $table->bigInteger('start_balance');
      $table->bigInteger('end_balance');
      $table->bigInteger('target_balance');
      $table->string('bot');
      $table->bigInteger('pay_in');
      $table->bigInteger('pay_out');
      $table->integer('low');
      $table->integer('high');
      $table->string('status')->default('lose');
      $table->boolean('is_finish')->default(false);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('history_bots');
  }
}
