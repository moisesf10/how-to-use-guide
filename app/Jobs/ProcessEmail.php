<?php

namespace App\Jobs;

use App\Events\EmailProcessed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ProcessEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Mailable $email;
    public $to = null;

    /**
     * Create a new job instance.
     */
    public function __construct(Mailable $email, $to)
    {
        $this->email = $email;
        $this->to = $to;

        $this->onQueue('emails');

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try{
            Mail::to($this->to)->send($this->email);
        }catch (\Exception $e){
            abort(500, $e->getMessage());
        }

    }



}
