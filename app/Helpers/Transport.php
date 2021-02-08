<?php

namespace App\Helpers;

class Transport{

    const PATH_DIR = '/storage/logs/files/';

    public static function saveFile(string $fileHash, string $content){

        $filePath = dirname(__DIR__ , 2) . self::PATH_DIR;
        if (!is_dir($filePath)) {
            if (!mkdir($filePath)){
                $isSaved = false;
            }else{
                $isSaved = file_put_contents($filePath . $fileHash, $content);
            }
        }else{
            $isSaved = file_put_contents($filePath . $fileHash, $content);
        }
        return $isSaved;
    }

    public static function deleteFile(string $fileHash){

        $filePath = dirname(__DIR__ , 2) . self::PATH_DIR;
        if (is_dir($filePath)){
            if (file_exists($filePath. $fileHash)) {
                $isDeleted = unlink($filePath . $fileHash);
            }
        }
        return $isDeleted;
    }
}
