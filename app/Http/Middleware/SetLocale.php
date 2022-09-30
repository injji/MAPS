<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App;
use Str;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param string|null $type
     * @return mixed
     */
    public function handle($request, Closure $next, $type = null)
    {        
        if ($type != null && method_exists($this, 'set'.Str::studly($type).'Locale')) {
            $this->{'set'.Str::studly($type).'Locale'}();
        }
        return $next($request);
    }

    /**
     * 클라이 언트 페이지 언어 설정
     *
     * @return void
     */
    public function setClientLocale()
    {
        if (Auth::check()) {
            App::setLocale(Auth::user()->lang);
        } else {
            App::setLocale(session()->get('store_lang') ?? 'ko');
        }
    }
}
