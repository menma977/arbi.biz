<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\DashboardController;
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
    Route::post("store", [AnnouncementController::class, 'store'])->name('store');
  });
});

require __DIR__ . '/auth.php';
