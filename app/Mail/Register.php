<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Register extends Mailable
{
  use Queueable, SerializesModels;

  protected $email;
  protected $username;
  protected $password;
  protected $wallet;

  /**
   * Create a new message instance.
   *
   * @param $email
   * @param $username
   * @param $password
   * @param $wallet
   */
  public function __construct($email, $username, $password, $wallet)
  {
    $this->email = $email;
    $this->username = $username;
    $this->password = $password;
    $this->wallet = $wallet;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->view('mail.register.index')
      ->from(env("MAIL_FROM_ADDRESS", "admin@Arbi.biz"))
      ->with([
        "email" => $this->email,
        "username" => $this->username,
        "password" => $this->password,
        "wallet" => $this->wallet,
      ]);
  }
}
