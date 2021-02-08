<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Helpers\Transport;

class FileController extends Controller
{
    public function receiveFile(Request $req)
    {
        $sault = 'MySault';
        $filename = $req->get('filename');
        $content = $req->get('content');
        $file_hash = md5($filename . $content . $sault);

        if (!File::where('file_hash', $file_hash)->first()) {

            $newFile = File::addFile($file_hash);

            $isSaved = Transport::saveFile($file_hash, $content);
            if ($isSaved === false) {
                $newFile->status = File::FILE_ERROR;
            } else {
                $newFile->status = File::FILE_SENT;
            }
            $newFile->save();
        }else{
            $newFile = File::where('file_hash', $file_hash)->first();
        }
        return response()->json(array('success' => true, 'status' => $newFile->status, 'id' => $newFile->file_hash));


    }

    public function getFileInfo(Request $req)
    {
        $fileHash = $req->get('id');
        if (!File::where('file_hash', $fileHash)->first()) {
            $result = array('success' => false);
        }else{
            $file = File::where('file_hash', $fileHash)->first();

            $result = array('success' => true, 'status' => $file->status);
        }

        return response()->json($result);
    }

    public function deleteFile(Request $req)
    {
        $fileHash = $req->get('id');
        if (!File::where('file_hash', $fileHash)->first()) {
            $result = array('success' => false);
        }else{
            $file = File::where('file_hash', $fileHash)->first();
            if ($file->status != File::FILE_DEL) {

                $isDeleted = Transport::deleteFile($file->file_hash);

                if ($isDeleted) {
                    $file->status = File::FILE_DEL;
                    $file->deleted_at = date("Y-m-d H:i:s");
                }else{
                    $file->status = File::FILE_ERROR;
                }
                $file->save();

                $result = array('success' => true, 'status' => $file->status);
            }else{
                $result = array('success' => true, 'status' => $file->status);
            }
        }

        return response()->json($result);
    }
}
