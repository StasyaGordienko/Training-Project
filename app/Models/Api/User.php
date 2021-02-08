<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $table = 'api_users';

    public static function addUser($username, $password, $requestCount=0, $lastRequestAt=null){

        $newUser = new self();
        $newUser->username = $username;
        $newUser->password = md5($password);
        $newUser->request_count = $requestCount;
        $newUser->last_request_at = $lastRequestAt;
        $newUser -> save();

        return $newUser;

    }

}
