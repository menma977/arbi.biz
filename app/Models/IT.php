<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class IT
 * @package App\Models
 * @property string created_at
 * @property string updated_at
 * @property integer id
 * @property string username
 * @property string password
 * @property string cookie
 * @property string wallet
 */
class IT extends Model
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
