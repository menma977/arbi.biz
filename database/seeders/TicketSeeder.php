<?php

namespace Database\Seeders;

use App\Models\Ticket;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $ticket = new Ticket();
    $ticket->user_id = 1;
    $ticket->description = "add pin";
    $ticket->debit = 1000000;
    $ticket->save();
  }
}
