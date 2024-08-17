<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendNotification extends Notification
{
    use Queueable;
//    private $data=[];
    private $obj_id;
    private $title;
    private $body;

    public function __construct($data)
    {
        $this->obj_id = $data['obj_id'];
        $this->title = $data['title'];
        $this->body = $data['body'];
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray( $notifiable): array
    {
        return [
            'obj_id'=>$this->obj_id,
            'title'=>$this->title,
            'body' => $this->body,
        ];
    }
}
