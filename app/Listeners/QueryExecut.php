<?php

namespace App\Listeners;

use Log;

class QueryExecut
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if (config('app.debug', false)) {
            $sql = $event->sql;
            if (count($event->bindings) > 0) {
                foreach ($event->bindings as $value) {
                    try {
                        $sql = preg_replace('/\?/', (string) $value, $sql, 1);
                    } catch (\Exception $e) {
                        $sql = preg_replace('/\?/', (string) $value->format('Y-m-d H:i:s'), $sql, 1);
                    }
                }
            }
            Log::channel('sql')->info($sql);
        }
    }
}
