<?php

namespace App\Jobs;

use App\Mail\ChangeEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ChangeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    public $data = [];

    public function __construct($data)
    {
        $this->data['code'] = $data['code'];
        $this->data['pre_email'] = $data['pre_email'];
        $this->data['new_email'] = $data['new_email'];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->data['pre_email'])->send(new ChangeEmail($this->data['code'], $this->data['pre_email'], $this->data['new_email']));
    }
}
