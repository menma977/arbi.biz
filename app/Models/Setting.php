<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Setting
 * @package App\Models
 * @property boolean maintenance
 * @property boolean logging
 * @property integer version
 * @property integer min_bot
 * @property integer max_bot
 * @property integer it
 * @property integer buy_wall
 * @property integer sponsor
 */
class Setting extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'maintenance',
    'logging',
    'version',
    'min_bot',
    'max_bot',
    'it',
    'buy_wall',
    'sponsor',
  ];
}
