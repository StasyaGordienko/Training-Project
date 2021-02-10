<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class User extends Model
{
    use HasFactory;

    protected $table = 'api_users';
    protected $guarded = [];

    /**
     * @param null $lastRequestAt
     */
    public static function addUser(string $username, string $password, int $requestCount = 0, $lastRequestAt = null): self
    {

        $newUser = app()->make( User::class, [
            'username' => $username,
            "password" => md5($password),
            "request_count" => $requestCount,
            "last_request_at" => $lastRequestAt,
        ]);
        /*$newUser->username = $username;
        $newUser->password = md5($password);
        $newUser->request_count = $requestCount;
        $newUser->last_request_at = $lastRequestAt;*/
        $newUser->save();

        return $newUser;
    }
}
