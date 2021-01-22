<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
  public function index()
  {
    $announcement = Announcement::first();

    $data = [
      'type' => $announcement->type,
      'title' => $announcement->title,
      'message' => $announcement->message,
    ];

    return response()->json($data);
  }
}
