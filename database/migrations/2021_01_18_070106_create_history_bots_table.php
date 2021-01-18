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
      $table->integer('bot')->default(1);
      $table->decimal('pay_in', 12, 8);
      $table->decimal('pay_out', 12, 8);
      $table->integer('low');
      $table->integer('high');
      $table->string('status')->default('lose');
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
