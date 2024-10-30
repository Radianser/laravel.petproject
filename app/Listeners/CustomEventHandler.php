<?php

namespace App\Listeners;

use App\Events\CustomEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CustomEventHandler
{
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
    public function handle(CustomEvent $event): void
    {
        echo $event->string;
        // echo "Hello, World!";
        // redirect(route('home.index'));
    }
}
