<?php

namespace Tests\Feature\API;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
  use RefreshDatabase;

  public function test_api_login_with_valid_credentials()
  {
    $user = User::factory();
    $this->post('/api/login', [
      "username" => $user->username,
      "password" => $user->password_mirror
    ]);
    $this->assertAuthenticated();
  }

  public function test_api_login_with_email()
  {
    $user = User::factory();
    $this->post('/api/login', [
      "username" => $user->email,
      "password" => $user->password_mirror
    ]);
    $this->assertAuthenticated();
  }

  public function test_api_login_with_phone()
  {
    $user = User::factory();
    $this->post('/api/login', [
      "username" => $user->phone,
      "password" => $user->password_mirror
    ]);
    $this->assertAuthenticated();
  }

  public function test_api_login_with_invalid_password()
  {
    $user = User::factory();
    $this->post('/api/login', [
      "username" => $user->username,
      "password" => "invalid password",
    ]);
    $this->assertGuest();
  }

  public function test_api_login_with_invalid_username()
  {
    $user = User::factory();
    $this->post('/api/login', [
      "username" => "InvalidUser",
      "password" => $user->password
    ]);
    $this->assertGuest();
  }

  public function test_api_login_as_suspended_user()
  {
    $user = User::factory(["suspend" => true]);
    $this->post('/api/login', [
      "username" => "InvalidUser",
      "password" => $user->password
    ]);
    $this->assertGuest();
  }

  public function test_api_register()
  {
    $response = $this->post('/api/register', [
      "name" => "Agustin Sentosa Anugrah",
      "email" => "SenAgustin@example.com",
      "username" => "Agustin21",
      "password" => "A341@acE!s",
      "confirmation_password" => "A341@acE!s",
    ]);
    $response->assertStatus(200)->assertJson(['code' => 200, "message" => "success"]);
  }

  public function test_api_register_with_pre_existing_username()
  {
    $user = User::factory();
    $response = $this->post('/api/register', [
      "name" => "Agustin Sentosa Anugrah",
      "email" => "SenAgustin@example.com",
      "username" => $user->username,
      "password" => "A341@acE!s",
      "confirmation_password" => "A341@acE!s",
    ]);
    $response->assertStatus(200)->assertJson(['code' => 200, "message" => "success"]);
  }

  public function test_api_register_with_mismatch_password()
  {
    $response = $this->post('/api/register', [
      "name" => "Agustin Sentosa Anugrah",
      "email" => "SenAgustin@example.com",
      "username" => "Agustin21",
      "password" => "A341@acE!s",
      "confirmation_password" => "A341@acE1s",
    ]);
    $response->assertStatus(200)->assertJson(['code' => 200, "message" => "success"]);
  }

  public function test_api_register_with_invalid_wallet()
  {
    $response = $this->post('/api/register', [
      "name" => "Agustin Sentosa Anugrah",
      "email" => "SenAgustin@example.com",
      "username" => "Agustin21",
      "password" => "A341@acE!s",
      "confirmation_password" => "A341@acE!s",
    ]);
    $response->assertStatus(200)->assertJson(['code' => 200, "message" => "success"]);
  }

  public function test_api_register_with_invalid_email()
  {
    $response = $this->post('/api/register', [
      "name" => "Agustin Sentosa Anugrah",
      "email" => "SenAgustin",
      "username" => "Agustin21",
      "password" => "A341@acE!s",
      "confirmation_password" => "A341@acE!s",
    ]);
    $response->assertStatus(200)->assertJson(['code' => 200, "message" => "success"]);
  }

  public function test_api_logout()
  {
    Passport::actingAs(User::factory());
    $response = $this->get("logout");
    $response->assertStatus(204);
  }
}
