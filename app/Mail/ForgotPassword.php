<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPassword extends Mailable
{
  use Queueable, SerializesModels;

  protected $code;
  protected $email;

  /**
   * Create a new message instance.
   *
   * @param $code
   * @param $email
   */
  public function __construct($code, $email)
  {
    $this->code = $code;
    $this->email = $email;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build(): self
  {
    return $this->view('mail.forgot.password')
      ->from(env("MAIL_FROM_ADDRESS", "admin@Arbi.biz"))
      ->with([
        "email" => $this->email,
        "code" => $this->code
      ]);
  }
}
