<?php

namespace App\Http\Controllers;

use App\Helpers\AuthBasic as AuthBasicCheck;
use App\Jobs\ProcessSendingFiles;
use App\Models\Api\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\File;
use App\Models\FileQueue;
use App\Helpers\Transport;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{

    public function receiveFile(Request $req): \Illuminate\Http\JsonResponse
    {
        $sault = 'MySault';
        $filename = $req->get('filename');
        $content = $req->get('content');
        $delay = $req->get('delay');

        if ($req->header('Authorization')) {

            $getUserName = AuthBasicCheck::authCheck($req->header('Authorization'));

            if (!$getUserName) {
                return response()->json(array('success' => false));
            }
            $fileHash = md5($filename . $getUserName . $content . $sault);

            if (!File::where('file_hash', $fileHash)->first()) {

                $getUser = User::query()->where('username','=', $getUserName)->first();
                $newFile = File::addFile($fileHash, $getUser->id);

                if (!$delay) {
                    $delay = 0;
                }

                ProcessSendingFiles::dispatch($newFile, $content)
                    ->onConnection('database')
                    ->delay(Carbon::now()->addMinutes($delay));

                return response()->json(array('success' => true,
                    'status' => $newFile->status,
                    'id' => $newFile->file_hash));
            } else {
                Log::channel('filelog')
                    ->debug('Cannot add a new file because it already exists');
                return response()->json(array('success' => false));
            }
        } else {
            Log::channel('authlog')
                ->debug('Authentication failed: there is no authorization information');
            return response()->json(array('success' => false));
        }
    }


    public function getFile(Request $req): \Illuminate\Http\JsonResponse
    {
        $fileHash = $req->get('id');
        if (!File::where('file_hash', $fileHash)->first()) {

            Log::channel('filelog')
                ->debug('File wasn\'t found', ['file_hash' => $fileHash]);

            $result = array('success' => false);
        } else {
            $file = File::where('file_hash', $fileHash)->first();

            $result = array('success' => true, 'status' => $file->status);
        }

        return response()->json($result);
    }


    public function deleteFile(Request $req): \Illuminate\Http\JsonResponse
    {
        $fileHash = $req->get('id');
        if (!File::where('file_hash', $fileHash)->first()) {

            Log::channel('filelog')
                ->debug('File wasn\'t found', ['file_hash' => $fileHash]);
            $result = array('success' => false);
        } else {
            $file = File::where('file_hash', $fileHash)->first();
            if ($file->status != File::STATUS_DEL) {

                $isDeleted = Transport::deleteFile($file->file_hash);

                if ($isDeleted) {
                    $file->status = File::STATUS_DEL;
                    $file->deleted_at = date("Y-m-d H:i:s");
                } else {
                    $file->status = File::STATUS_ERROR;
                }
                $file->save();

                $result = array('success' => true, 'status' => $file->status);
            } else {
                $result = array('success' => true, 'status' => $file->status);
            }
        }

        return response()->json($result);
    }
}
