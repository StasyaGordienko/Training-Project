<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileQueue extends Model
{
    use HasFactory;

    const STATUS_NEW = 'new';
    const STATUS_PROCESSING = 'processing';

    protected $table = 'file_queue';

    /**
     * @param $fileHash
     * @param $content
     * @param $delay
     * @return FileQueue
     */
    public static function addFileToQueue($fileHash, $content, $delay){

        $fileToQueue = new self();
        $fileToQueue->file_id = $fileHash;
        $fileToQueue->content = $content;
        $fileToQueue->delay = $delay;
        $fileToQueue->save();

        return $fileToQueue;

    }
}
