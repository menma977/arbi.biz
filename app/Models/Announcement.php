<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Announcement
 * @package App\Models
 * @property string created_at
 * @property string updated_at
 * @property string type = [info, warning, danger]
 * @property string title
 * @property string description
 */
class Announcement extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'type', // ['info', 'warning', 'danger']
    'title',
    'description',
  ];
}
