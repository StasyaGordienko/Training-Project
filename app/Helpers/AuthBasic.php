<?php

namespace App\Helpers;

use App\Models\Api\User;
use Illuminate\Support\Facades\Log;

class AuthBasic{
    /**
     * @param $authHeader
     * @return User|false
     */

    public static function authCheck($authHeader)
    {

        $authLine = base64_decode($authHeader, true);

        if ($authLine){
            $authData = explode(',', $authLine);

            if (count($authData) > 1){
                $getUser = User::where('username',$authData[0])->where('password',md5($authData[1]))->first();

                if (!$getUser){
                    return false;
                }else{

                    return $getUser;
                }
            }else{
                return false;
            }
        }else {
            return false;
        }
    }
}
