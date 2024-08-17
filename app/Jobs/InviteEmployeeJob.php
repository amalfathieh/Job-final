<?php

namespace App\Jobs;

use App\Mail\InviteEmployeeEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class InviteEmployeeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    private $data = [] ;
    public function __construct($email, $password,$link)
    {
        $this->data = ['email' => $email, 'password' => $password , 'link' => $link];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->data['email'])->send(new InviteEmployeeEmail($this->data['password'],$this->data['link']));
    }
}
