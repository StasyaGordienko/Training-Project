<?php

namespace App\Http\Controllers;

use App\Helpers\AuthBasic as AuthBasicCheck;
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

        $getUser = AuthBasicCheck::authCheck($req->header('Authorization'));
        if (!$getUser){
            return response()->json(array('success' => false));
        }
        $fileHash = md5($filename . $getUser->id . $content . $sault);

        if (!File::where('file_hash', $fileHash)->first()) {

            $newFile = File::addFile($fileHash, $getUser->id);

            $isSaved = Transport::saveFile($fileHash, $content);
            if ($isSaved === false) {
                $newFile->status = File::FILE_ERROR;
            } else {
                $newFile->status = File::FILE_SENT;
            }
            $newFile->save();
        }else{
            $newFile = File::where('file_hash', $fileHash)->first();
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
