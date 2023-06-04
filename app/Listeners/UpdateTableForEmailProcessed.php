<?php

namespace App\Listeners;


use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateTableForEmailProcessed
{
    public $queue = 'emails';

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
       // Log::info(print_r($event?->data[''], true));
      //  Log::info(print_r($event, true));

      //  throw new \Exception('falha lançada');

        try{
            if(! empty($event?->data['mailName'])){
                switch ($event?->data['mailName']){
                    case 'AuthorizeEditorMail':
                        $event?->data['editor']->update([
                            'status_send_mail'  =>  'success',
                            'last_attemp_send_mail' =>  now(),
                        ]);
                        break;
                }
            }
        }catch (QueryException $e){
            Log::info($e->getMessage(). PHP_EOL . $e->getTraceAsString());
            throw $e;
        }

    }

    public function failed($event, Throwable $exception): void
    {
        //Log::info(print_r('FAILED', true));
       // Log::info(print_r($event, true));

        if(! empty($event?->data['mailName'])){
            switch ($event?->data['mailName']){
                case 'AuthorizeEditorMail':
                    $event?->data['editor']->editor->update([
                        'status_send_mail'  =>  'error',
                        'last_attemp_send_mail' =>  now(),
                        'mail_error'    =>  $exception->getMessage()
                    ]);
                    break;
            }
        }

    }
}
