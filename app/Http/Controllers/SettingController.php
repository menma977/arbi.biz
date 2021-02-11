<?php

namespace App\Http\Controllers;

use App\Events\Bot;
use App\Events\Maintenance;
use App\Models\Setting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SettingController extends Controller
{
  /**
   * @return Application|Factory|View
   */
  public function index()
  {
    $setting = Setting::first();

    $data = [
      'setting' => $setting,
    ];

    return view('setting.index', $data);
  }

  /**
   * @param $switch
   * @return RedirectResponse
   */
  public function maintenance($switch): RedirectResponse
  {
    $setting = Setting::first();
    $setting->maintenance = (boolean)$switch;
    $setting->save();

    if ((boolean)$switch) {
      $this->removeUser(true);
      return redirect()->back()->with(["message" => "Maintenance has been active"]);
    }

    $this->removeUser(false);
    return redirect()->back()->with(["message" => "Maintenance has been deactivate"]);
  }

  /**
   * @param Request $request
   * @return RedirectResponse
   * @throws ValidationException
   */
  public function version(Request $request): RedirectResponse
  {
    $this->validate($request, [
      'version' => "required|min:1"
    ]);

    $setting = Setting::first();
    $setting->version = $request->input("version");
    $setting->save();

    $this->removeUser(false);

    return redirect()->back()->with(["message" => "Version has been update"]);
  }

  /**
   * @param Request $request
   * @return RedirectResponse
   * @throws ValidationException
   */
  public function bot(Request $request): RedirectResponse
  {
    $this->validate($request, [
      'min' => "required|min:0.00000001",
      'max' => "required|min:0.00000001",
      'it' => "required|between:0.01,100",
      'buy_wall' => "required|between:0.01,100",
      'sponsor' => "required|between:0.01,100",
    ]);

    $setting = Setting::first();
    $setting->min_bot = self::formatDecimal($request->input("min"));
    $setting->max_bot = self::formatDecimal($request->input("max"));
    $setting->it = $request->input("it") / 100;
    $setting->buy_wall = $request->input("buy_wall") / 100;
    $setting->sponsor = $request->input("sponsor") / 100;
    $setting->save();

    event(new Bot($setting->min_bot, $setting->max_bot, $setting->it, $setting->sponsor, $setting->buy_wall));

    return redirect()->back()->with(["message" => "BOT has been update"]);
  }

  private static function formatDecimal($value): string
  {
    return number_format($value, 8, '', '');
  }

  private function removeUser($maintenance): void
  {
    DB::table("oauth_access_tokens")->delete();
    event(new Maintenance($maintenance));
  }
}
