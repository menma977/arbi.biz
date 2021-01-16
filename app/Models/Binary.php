<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Binary
 * @package App\Models
 * @property string created_at
 * @property string updated_at
 * @property integer sponsor
 * @property integer down_line
 */
class Binary extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'sponsor',
    'down_line',
  ];
}
