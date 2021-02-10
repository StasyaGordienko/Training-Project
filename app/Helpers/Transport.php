<?php

namespace App\Helpers;

use App\Models\FileQueue;

class Transport{

    const PATH_DIR = '/storage/logs/files/';


    public static function sendFile(FileQueue $fileQueue):bool
    {
        $filePath = dirname(__DIR__ , 2) . self::PATH_DIR;
        if (!is_dir($filePath) && !mkdir($filePath)) {

            throw new \Exception("Cannot write to the directory {$filePath}");
        }

        return (bool)file_put_contents($filePath . $fileQueue->file_hash, $fileQueue->content);
    }

    /**
     * @param string $fileHash
     * @return bool
     */

    public static function deleteFile(string $fileHash){

        $isDeleted = false;
        $filePath = dirname(__DIR__ , 2) . self::PATH_DIR;
        if (is_dir($filePath)){
            if (file_exists($filePath. $fileHash)) {
                $isDeleted = unlink($filePath . $fileHash);
            }
        }
        return $isDeleted;
    }
}
