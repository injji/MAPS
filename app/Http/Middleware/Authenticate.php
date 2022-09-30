<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Auth\AuthenticationException;
use Closure;
use Str;

class Authenticate extends Middleware
{
    protected $guards = null;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        if (count($guards)) {
            if (method_exists($this, 'handle'.Str::studly($guards[0]))) {                
                [$guards, $handle] = $this->{'handle'.Str::studly($guards[0])}();
            }
            $this->guard = $guards[0];
        }
        $response = parent::handle($request, $next, ...($guards ?? []));
        if (isset($handle) && $handle != null) {
            $handle($request);
        }
        return $response;
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson() && $request->hasSession()) {
            $request->session()->put('redirectUrl', $request->fullUrl());
            if ($request->expectsJson()) {
                abort(401);
            } elseif ($this->guard == 'user') {
                return config('app.url').'/login';
            } else {
                return 'login';
            }
        }
    }

    /**
     * client 회원 handle
     *
     * @return [string, closer]
     */
    protected function handleClient()
    {
        $self = $this;
        return [
            ['user'],
            function($request) use ($self) {                
                if ($request->user() && $request->user()->type == 2) {                    
                    throw new AuthenticationException(
                        'Unauthenticated.', ['client'], config('app.pre_url').'://'.config('app.domain.agent')
                    );
                }
            }
        ];
    }

    /**
     * agent 회원 handle
     *
     * @return [string, closer]
     */
    protected function handleAgent()
    {        
        $self = $this;
        return [
            ['user'],
            function($request) use ($self) {
                if ($request->user() && $request->user()->type == 1) {
                    throw new AuthenticationException(
                        'Unauthenticated.', ['agent'], config('app.pre_url').'://'.config('app.domain.client')
                    );
                }
            }
        ];
    }

    /**
     * cms handle
     *
     * @return [string, closer]
     */
    protected function handleCms()
    {
        $self = $this;
        return [
            ['cms'],
            function($request) use ($self) {
                
            }
        ];
    }
}
