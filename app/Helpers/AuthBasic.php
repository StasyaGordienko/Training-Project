<?php

namespace App\Helpers;

use App\Models\Api\User;
use Illuminate\Support\Facades\Log;

class AuthBasic{
    /**
     * @return User|false
     */

    public static function authCheck(string $authHeader)
    {

        $authLine = base64_decode($authHeader, true);

        if ($authLine){
            $authData = explode(',', $authLine);

            if (!empty($authData) && count($authData) > 1){
                $getUser = User::where('username',$authData[0])->where('password',md5($authData[1]))->first();

                if (!$getUser){
                    Log::channel('authlog')->debug('User wasn\'t found');
                    return false;
                }else{

                    return $getUser;
                }
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
