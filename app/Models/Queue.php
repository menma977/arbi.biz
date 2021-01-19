<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Queue
 * @package App\Models
 * @property string created_at
 * @property string updated_at
 * @property integer user_id
 * @property string type //it, buy_wall, sponsor
 * @property string value
 * @property boolean send
 */
class Queue extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'user_id',
    'type',
    'value',
    'send',
  ];
}
