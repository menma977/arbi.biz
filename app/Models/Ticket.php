<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Ticket
 * @package App\Models
 * @property string created_at
 * @property string updated_at
 * @property integer user_id
 * @property string description
 * @property double debit
 * @property double credit
 */
class Ticket extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'user_id',
    'description',
    'debit',
    'credit',
  ];
}
