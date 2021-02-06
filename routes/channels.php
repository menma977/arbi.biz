<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

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

Broadcast::channel('arbi.biz.{username}', function ($user, $username) {
  Log::debug('channles.php');
  Log::debug($user->username);
  Log::debug($username);
  return true;
//  return $user->username === $username;
});
