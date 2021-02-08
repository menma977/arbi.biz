<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;

class AnnouncementController extends Controller
{
  /**
   * @return JsonResponse
   */
  public function index(): JsonResponse
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
