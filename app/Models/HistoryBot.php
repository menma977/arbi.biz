<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HistoryBot
 * @package App\Models
 * @property string created_at
 * @property string updated_at
 * @property integer bot
 * @property double pay_in
 * @property double pay_out
 * @property integer low
 * @property integer high
 * @property string status
 */
class HistoryBot extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'bot',
    'pay_in',
    'pay_out',
    'low',
    'high',
    'status',
  ];
}
