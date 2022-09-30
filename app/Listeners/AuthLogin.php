<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;

class AuthLogin
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if (method_exists($this, 'handle'.Str::studly($event->guard).'Event')) {
            $this->{'handle'.Str::studly($event->guard).'Event'}($event);
        }
    }

    /**
     * CMS 로그인 이벤트 처리
     *
     * @param Illuminate\Auth\Events\Login $event
     * @return void
     */
    public function handleCmsEvent($event)
    {
        // $event->user->loginLog();
    }

    /**
     * Front 로그인 이벤트 처리
     *
     * @param Illuminate\Auth\Events\Login $event
     * @return void
     */
    public function handleFrontEvent($event)
    {
        $event->user->loginLog();
    }
}
