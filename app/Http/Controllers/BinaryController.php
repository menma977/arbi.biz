<?php

namespace App\Http\Controllers;

use App\Models\Binary;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BinaryController extends Controller
{
  /**
   * @return Application|Factory|View
   */
  public function index()
  {
    $binary = Binary::where('sponsor', Auth::user()->id)->get();
    $binary->map(function ($item) {
      $item->down_line = User::find($item->down_line);

      return $item;
    });

    $data = [
      'binary' => $binary,
    ];

    return view('binary.admin', $data);
  }

  /**
   * @param $id
   * @return mixed
   */
  public function show($id)
  {
    $binary = Binary::where('sponsor', $id)->get();
    $binary->map(function ($item) {
      $item->down_line = User::find($item->down_line);

      return $item;
    });

    return $binary;
  }
}
