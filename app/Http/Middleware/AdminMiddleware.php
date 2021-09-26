<?php

namespace App\Http\Middleware;

use App\Traits\SendResponse;
use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    use SendResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return  auth()->user()->permissions[0]->name === 'admin' ?
            $next($request) : $this->send_response(401, 'غير مصرح لك بالدخول الى هذه الصفحة', [], []);
    }
}