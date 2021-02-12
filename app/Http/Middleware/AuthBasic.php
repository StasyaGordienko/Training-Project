<?php

namespace App\Http\Middleware;

use App\Models\Api\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\AuthBasic as AuthBasicCheck;
use Illuminate\Support\Facades\Cache;
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
            $getUserName = AuthBasicCheck::authCheck($request->header('Authorization'));
            if (!$getUserName) {

                Log::channel('authlog')->debug('Authentication failed');
                return response()->json(['message', 'Authentication failed']);

            } else {
                $getUser = User::query()->where('username','=', $getUserName)->first();
                $getUser->request_count++;
                $getUser->last_request_at = now();
                $getUser->save();

                Cache::store('database')->put($getUserName, md5($getUser->password), 600);

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
