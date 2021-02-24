<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('users', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('email')->unique();
      $table->string('username')->unique();
      $table->timestamp('email_verified_at')->nullable();
      $table->string('password');
      $table->string('password_mirror');
      $table->string('pin')->default(0);
      $table->text('trade_fake')->nullable();
      $table->text('trade_real')->nullable();
      $table->boolean('suspend')->default(false);
      $table->ipAddress('last_ip')->nullable();
      $table->rememberToken();
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
    Schema::dropIfExists('users');
  }
}
