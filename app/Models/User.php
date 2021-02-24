<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Class User
 * @package App\Models
 * @property integer id
 * @property string name
 * @property string email
 * @property string username
 * @property string password
 * @property string password_mirror
 * @property string pin
 * @property string trade_fake
 * @property string trade_real
 * @property Boolean suspend
 * @property string last_ip
 * @property string created_at
 * @property string updated_at
 */
class User extends Authenticatable
{
  use HasFactory, Notifiable, HasApiTokens;

  protected $with = ['coinAuth'];

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name',
    'email',
    'username',
    'password',
    'password_mirror',
    'pin',
    'trade_fake',
    'trade_real',
    'suspend',
    'last_ip',
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password',
    'remember_token',
    'last_ip',
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  public function coinAuth()
  {
    return $this->hasOne(CoinAuth::class, 'user_id', 'id');
  }
}
