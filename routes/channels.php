<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Route::middleware(['auth:api'])->group(function () {

});

Broadcast::channel('arbi.biz.ticket.{username}', function ($user, $id) {
  return $user ? true : false;
});
