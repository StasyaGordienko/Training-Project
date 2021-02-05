<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;

class FileController extends Controller
{
    public function receiveFile(Request $req)
    {
        $sault = 'MySault';
        $data = $req->all();
        $file_hash = sha1(md5($data['filename'] . $data['content'] . $sault));

        if (!File::where('file_hash', $file_hash)->first()) {

            $new_file = new File();
            $new_file->file_hash=$file_hash;
            $new_file->status = 'received';
            $new_file->deleted_at = '0000-01-01';
            $new_file->save();

            $is_saved = file_put_contents($file_hash, $data['content']);
            if ($is_saved === false) {
                $new_file->status = 'error';
            } else {
                $new_file->status = 'sent';
            }
            $new_file->save();
        }else{
            $new_file = File::where('file_hash', $file_hash)->first();
        }
        return response()->json(array('success' => true, 'status' => $new_file->status, 'id' => $new_file->id));


    }

    public function getFileInfo(Request $req)
    {
        if (!File::find($req->all()['id'])) {
            $result = array('success' => false);
        }else{
            $file = File::find($req->all()['id']);

            $result = array('success' => true, 'status' => $file->status);
        }

        return response()->json($result);
    }

    public function deleteFile(Request $req)
    {
        if (!File::find($req->all()['id'])) {
            $result = array('success' => false);
        }else{
            $file = File::find($req->all()['id']);
            if ($file->status !='deleted') {

                if (file_exists($file->file_hash)){
                    unlink($file->file_hash);
                }
                $file->status = 'deleted';
                $file->deleted_at = date("Y-m-d H:i:s");
                $file->save();

                $result = array('success' => true, 'status' => $file->status);
            }else{
                $result = array('success' => true, 'status' => $file->status);
            }
        }

        return response()->json($result);
    }
}
