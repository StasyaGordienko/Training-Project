<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    const FILE_RECEIVED = "received";
    const FILE_SENT = "sent";
    const FILE_DEL = "deleted";
    const FILE_ERROR = "error";

    use HasFactory;

    public static function addFile(string $file_hash){

        $newFile = new self();
        //$newFile->user_id =
        $newFile->file_hash=$file_hash;
        $newFile->status = self::FILE_RECEIVED;
        $newFile->save();

        return $newFile;
    }
}
