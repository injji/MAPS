<?php

namespace App\Http\Middleware;

use Illuminate\Session\Middleware\StartSession as BaseStartSession;

use Closure;

class StartSession extends BaseStartSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (parse_url($request->fullUrl(), PHP_URL_HOST) == config('app.domain.cms'))
        {            
            config([
                'session.cookie' => 'mapstrend_cms_session',
                'session.table' => 'tbl_cms_sessions',
                'session.domain' => config('app.domain.cms'),
            ]);
        }
        return parent::handle(...func_get_args());
    }
}
