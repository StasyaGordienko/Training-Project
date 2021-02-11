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

    public static function addUser(string $username, string $password, int $requestCount = 0, $lastRequestAt = null): self
    {

        $newUser = User::create( [
            'username' => $username,
            "password" => md5($password),
            "request_count" => $requestCount,
            "last_request_at" => $lastRequestAt,
        ]);
        $newUser->save();

        return $newUser;
    }
}
