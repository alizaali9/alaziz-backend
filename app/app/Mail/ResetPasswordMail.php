<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $email;

    /**
     * Create a new message instance.
     */

    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;

    }


    /**
     * Build the message.
     *
     * @return $this
     */

    public function build()
    {
        return $this->view('emails.reset-password')
                    ->subject('Reset Password Notification')
                    ->with([
                        'token' => $this->token,
                        'email' => $this->email,
                        'url' => url('/reset-password?token=' . $this->token . '&email=' . urlencode($this->email)),
                    ]);
    }

}
