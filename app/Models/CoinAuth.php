<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class CoinAuth
 * @package App\Models
 * @property integer user_id
 * @property string username
 * @property string password
 * @property string cookie
 * @property string wallet
 * @property string created_at
 * @property string updated_at
 */
class CoinAuth extends Model
{
  use HasFactory;

  protected $primaryKey = 'user_id';
  protected $keyType = 'integer';
  public $incrementing = false;


  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'user_id',
    'username',
    'password',
    'cookie',
    'wallet',
  ];

  /**
   * @return BelongsTo
   */
  public function user()
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }
}
