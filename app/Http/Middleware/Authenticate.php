<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        /*dd( $request->route()->getPrefix() );

        dd(auth()->guard('admin')->check());
        dd(auth()->guard('admin')->user());

        if($request->expectsJson()){
            return null;
        }else{

        }*/

        return $request->expectsJson() ? null : route('index');
    }
}
