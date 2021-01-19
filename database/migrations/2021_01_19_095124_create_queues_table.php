<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueuesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('queues', function (Blueprint $table) {
      $table->timestamps();
      $table->bigInteger('user_id')->index();
      $table->enum('type', ['it', 'buy_wall', 'sponsor']);
      $table->decimal('value', 12, 8);
      $table->boolean('send');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('queues');
  }
}
