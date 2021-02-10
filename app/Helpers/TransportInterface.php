<?php

namespace App\Helpers;

use App\Models\FileQueue;

interface TransportInterface {

    public static function sendFile(FileQueue $fileQueue):bool;

    public static function deleteFile(string $fileHash):bool;
}
