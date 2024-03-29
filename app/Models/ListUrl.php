<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ListUrl
 * @package App\Models
 * @property string created_at
 * @property string updated_at
 * @property string url
 * @property string block
 * @property string start_at
 */
class ListUrl extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'url',
    'block',
    'start_at',
  ];
}
