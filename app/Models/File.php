<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class File
 * @package App\Models
 *
 * @property integer $id
 * @property integer user_id
 * @property string file_hash
 * @property string $status
 * @property Carbon $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class File extends Model
{
    use HasFactory;
    use SoftDeletes;

    const STATUS_RECEIVED = "received";
    const STATUS_SENT = "sent";
    const STATUS_DEL = "deleted";
    const STATUS_ERROR = "error";


    public static function addFile(string $fileHash, int $userId):self
    {

        $newFile = app()->make('App\Models\File', [
            "user_id" => $userId,
            "file_hash" => $fileHash,
            "status" => self::STATUS_RECEIVED
        ]);
        $newFile->user_id = $userId;
        $newFile->file_hash = $userId;
        $newFile->user_id = $userId;

        $newFile->save();

        return $newFile;
    }
}
