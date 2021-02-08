<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Api\User;

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
        $authLine = base64_decode($request->header('Authorization'), true);
        if ($authLine){
            $authData = explode(',', $authLine);
            if (count($authData) > 1){
                $getUser = User::where('username',$authData[0])->where('password',md5($authData[1]))->first();

                if (!$getUser){
                    return response()->json(['message','Authentication failed']);
                }else{
                    $getUser->request_count ++;
                    $getUser->last_request_at = now();
                    $getUser->save();

                    return $next($request);
                }
            }
        }else{
            return response()->json(['message','Authentication failed']);
        }

    }
}
