<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // if(auth()->guard('web')->user())
            // {
            //     return route('home.index');
            // }
            // else if(auth()->guard('admin')->user())
            // {
            //     return route('admin.category.index');
            // }
            // else
            // {
            //     return route('admin.login.show');
            // }
        }
    }
}
