<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Waiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class verifyApiToken
{
    /**
     * Handle an incoming request to check wheather is logged in or not.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(is_null($request->headers->get("Authorization"))){
            return response()->json([
                'success'   => false,
                'message'   => "Please log in to proceed",
                'errorData' => NULL
            ], Response::HTTP_UNAUTHORIZED);
            die();
        }
        if (Waiter::where("remember_token", "=", $request->headers->get("Authorization"))->first() instanceof Waiter) {
            return $next($request);
        }else{
            return response()->json([
                'success'   => false,
                'message'   => "Please log in to proceed",
                'errorData' => NULL
            ], Response::HTTP_UNAUTHORIZED);
            die();
        }
    }
}
