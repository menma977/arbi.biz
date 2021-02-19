<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HistoryPin
 * @package App\Models
 * @property string created_at
 * @property string updated_at
 * @property integer user_id
 * @property string description
 * @property integer value
 */
class HistoryPin extends Model
{
  use HasFactory;

  protected $primaryKey = "created_at";
  protected $keyType = "string";

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'user_id',
    'description',
    'value',
  ];
}
