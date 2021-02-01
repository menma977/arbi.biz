<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BuyWall
 * @package App\Models
 * @property string created_at
 * @property string updated_at
 * @property integer id
 * @property string wallet
 */
class BuyWall extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'username',
    'password',
    'cookie',
    'wallet',
  ];
}
