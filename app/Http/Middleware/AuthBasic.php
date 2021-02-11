<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\AuthBasic as AuthBasicCheck;
use Illuminate\Support\Facades\Log;

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
        if ($request->header('Authorization')) {
            $getUser = AuthBasicCheck::authCheck($request->header('Authorization'));
            if (!$getUser) {

                Log::channel('authlog')->debug('Authentication failed');
                return response()->json(['message', 'Authentication failed']);

            } else {

                $getUser->request_count++;
                $getUser->last_request_at = now();
                $getUser->save();

                return $next($request);
            }
        }
        else{
            Log::channel('authlog')
                ->debug('Authentication failed: there is no authorization information');
            return response()->json(['message','Authentication failed']);
        }
    }
}
