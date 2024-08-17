<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InviteEmployeeEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $password ,$link;

    public function __construct($password,$link)
    {
        $this->password = $password;
        $this->link = $link;
    }
    public function build()
    {
        return $this->markdown('emails.InviteEmployee');
    }
}
