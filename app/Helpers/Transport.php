<?php

namespace App\Helpers;

use App\Models\FileQueue;
use Illuminate\Support\Facades\Log;

class Transport implements TransportInterface {

    const PATH_DIR = '/storage/logs/files/';


    public static function sendFile(FileQueue $fileQueue):bool
    {
        $filePath = dirname(__DIR__ , 2) . self::PATH_DIR;

        if (!is_dir($filePath) && !mkdir($filePath)) {

            Log::channel('filelog')->debug('Wrong directory', ['filePath' => $filePath]);

            throw new \Exception("Cannot write to the directory " . $filePath);

        }
        return (bool)file_put_contents($filePath . $fileQueue->file_id, $fileQueue->content);
    }


    public static function deleteFile(string $fileHash):bool
    {
        $isDeleted = false;
        $filePath = dirname(__DIR__ , 2) . self::PATH_DIR;
        if (is_dir($filePath)){
            if (file_exists($filePath. $fileHash)) {
                $isDeleted = unlink($filePath . $fileHash);
            }else{
                Log::channel('filelog')
                    ->debug('File doesn\'t exist: ', ['filePath' => $filePath . $fileHash]);
            }
        }else{
            Log::channel('filelog')
                ->debug('There is no this directory: ', ['filePath' => $filePath]);
        }
        return $isDeleted;
    }
}
