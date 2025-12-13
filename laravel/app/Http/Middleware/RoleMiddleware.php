<?php

// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use Symfony\Component\HttpFoundation\Response;

// class RoleMiddleware
// {
//     /**
//      * Handle an incoming request.
//      *
//      * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
//      */
//     public function handle(Request $request, Closure $next): Response
//     {
//         return $next($request);
//     }
// }
namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware {
    public function handle($request, Closure $next, ...$roles) {
        if (!Auth::check() || !in_array(Auth::user()->role,$roles)) {
            return response()->json(['message'=>'Forbidden'],403);
        }
        return $next($request);
    }
}