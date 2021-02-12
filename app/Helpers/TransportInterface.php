<?php

namespace App\Helpers;

use App\Models\File;

interface TransportInterface {

    public static function sendFile(File $file, string $content):bool;

    public static function deleteFile(string $fileHash):bool;
}
