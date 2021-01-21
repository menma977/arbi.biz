<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Class HistoryBot
 * @package App\Models
 * @property string created_at
 * @property string updated_at
 * @property integer user_id
 * @property integer start_balance
 * @property integer end_balance
 * @property integer target_balance
 * @property string bot
 * @property double pay_in
 * @property double pay_out
 * @property integer low
 * @property integer high
 * @property string status
 * @property Boolean is_finish
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
    'user_id',
    'start_balance',
    'end_balance',
    'target_balance',
    'bot',
    'pay_in',
    'pay_out',
    'low',
    'high',
    'status',
    'is_finish',
  ];
}
