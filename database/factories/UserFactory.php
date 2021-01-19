<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = User::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    $password = $this->faker->password;
    return [
      'name' => $this->faker->name,
      'email' => $this->faker->unique()->safeEmail(),
      'username' => $this->faker->unique()->username(),
      'password' => Hash::make($password),
      'password_mirror' => $password,
      'trade_fake' => "",
      'trade_real' => "",
      'suspend' => false,
      'last_ip' => $this->faker->ipv4,
    ];
  }
}
