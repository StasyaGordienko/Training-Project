<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\AuthBasic as AuthBasicCheck;

class AuthBasic
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $getUser = AuthBasicCheck::authCheck($request->header('Authorization'));
        if (!$getUser) {

            return response()->json(['message','Authentication failed']);

        }else{

            $getUser->request_count ++;
            $getUser->last_request_at = now();
            $getUser->save();

            return $next($request);
        }

    }
}
