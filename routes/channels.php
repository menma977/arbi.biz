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

Broadcast::channel('arbi.biz.{username}.ticket', function ($user, $id) {
  return $user->id === $id;
});

Broadcast::channel('arbi.biz.{username}.treding', function ($user, $id) {
  return $user->id === $id;
});
