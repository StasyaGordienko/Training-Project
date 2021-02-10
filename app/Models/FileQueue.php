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
    protected $guarded = [];

    public static function addFileToQueue(string $fileHash, string $content, int $delay):self
    {
        $fileToQueue = FileQueue::create([
            'file_id' => $fileHash,
            'content' => $content,
            'delay' => $delay
        ]);
        $fileToQueue->save();

        return $fileToQueue;

    }

}
