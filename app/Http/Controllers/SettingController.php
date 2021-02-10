<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
  public function index()
  {
    $setting = Setting::first();

    $data = [
      'setting' => $setting,
    ];

    return view('setting.index', $data);
  }


}
