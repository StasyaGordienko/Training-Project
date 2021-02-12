<?php

namespace App\Helpers;

use App\Models\Api\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AuthBasic{
    /**
     * @return string|false
     */

    public static function authCheck(string $authHeader)
    {

        $authLine = base64_decode($authHeader, true);

        if ($authLine){
            $authData = explode(',', $authLine);

            if (!empty($authData) && count($authData) > 1){

                if (Cache::store('database')->has($authData[0])) {
                   return $authData[0];
                }else{
                    $getUser = User::where('username', $authData[0])->first();
                    if ($getUser->password == md5($authData[1])) {
                        return $getUser->username;
                    }
                }
                Log::channel('authlog')->debug('Wrong password');
                return false;

            }else{
                Log::channel('authlog')->debug('Authorization data is incorrect');
                return false;
            }
        }else {
            Log::channel('authlog')->debug('Impossible to decode');
            return false;
        }
    }
}
