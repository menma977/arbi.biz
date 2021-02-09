<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\BinaryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
  return view('welcome');
})->name("welcome");

Route::middleware(['auth'])->group(function () {
  Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::get("", [DashboardController::class, 'index'])->name('index');
  });

  Route::group(['prefix' => 'notification', 'as' => 'notification.'], function () {
    Route::get("index", [AnnouncementController::class, 'index'])->name('index');
    Route::post("store", [AnnouncementController::class, 'store'])->name('store');
  });

  Route::group(['prefix' => 'ticket', 'as' => 'ticket.'], function () {
    Route::get("index", [TicketController::class, 'index'])->name('index');
    Route::post("store", [TicketController::class, 'store'])->name('store');
    Route::post("remove", [TicketController::class, 'update'])->name('remove');
  });

  Route::group(['prefix' => 'binary', 'as' => 'binary.'], function () {
    Route::get("index", [BinaryController::class, 'index'])->name('index');
    Route::get("show", [BinaryController::class, 'show'])->name('show');
  });
});

require __DIR__ . '/auth.php';
