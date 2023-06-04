<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;
        dd( $request->route()->getPrefix()  );
        foreach ($guards as $guard) {

            if (Auth::guard($guard)->check()) {
                switch ($guard){
                    case 'admin':
                        return redirect(route('admin_index'));
                        break;
                    default:
                        return redirect(route('guide_index'));
                        break;
                }

            }
        }

        return $next($request);
    }
}
