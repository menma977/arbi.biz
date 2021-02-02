<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AnnouncementController extends Controller
{
  /**
   * @param Request $request
   * @return RedirectResponse
   * @throws ValidationException
   */
  public function store(Request $request): RedirectResponse
  {
    $this->validate($request, [
      'type' => 'required|in:info,warning,danger',
      'title' => 'required',
      'message' => 'required',
    ]);

    if (Announcement::get()->count()) {
      $announcement = Announcement::first();
      $announcement->type = $request->input('type');
      $announcement->title = $request->input('title');
      $announcement->description = $request->input('message');
      $announcement->save();

      event(new \App\Events\Announcement($announcement->title, $announcement->description, $announcement->type));

      return redirect()->back()->with(['message' => 'announce has update']);
    }

    $announcement = new Announcement();
    $announcement->type = $request->input('type');
    $announcement->title = $request->input('title');
    $announcement->description = $request->input('message');
    $announcement->save();

    event(new \App\Events\Announcement($announcement->title, $announcement->description, $announcement->type));

    return redirect()->back()->with(['message' => 'announce has create']);
  }
}
