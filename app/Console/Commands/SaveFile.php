<?php

namespace App\Console\Commands;

use App\Models\File;
use App\Models\FileQueue;
use App\Helpers\Transport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaveFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'save:file';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Saving files from the file_queue table on FTP';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle():int
    {
        $filesBuilder = FileQueue::query()
            ->where('delay', '<=', DB::raw('timestampdiff(minute,created_at,now())'));

        $filesBuilder->chunk(10, function ($fileQueueCollection) {
            foreach ($fileQueueCollection as $fileQueue) {

                $readyForSend = FileQueue::query()
                    ->where('id', $fileQueue->id)
                    ->where('status', FileQueue::STATUS_NEW)
                    ->update(['status' => FileQueue::STATUS_PROCESSING]);

                if (!$readyForSend) {
                    return false;
                }
                $isSent = Transport::sendFile($fileQueue);
                if ($isSent) {
                    $fileQueue->delete();

                    $getFile = File::query()->where('file_hash', '=', $fileQueue->file_id)->first();
                    if ($getFile) {
                        $getFile->status = File::STATUS_SENT;
                        $getFile->save();
                    }else{
                        Log::channel('filelog')
                            ->debug('File status wasn\'t changed', ['file_hash' => $fileQueue->id]);
                    }
                } else {
                    FileQueue::query()
                        ->where('id', $fileQueue->id)
                        ->where('status', FileQueue::STATUS_PROCESSING)
                        ->update(['status' => FileQueue::STATUS_NEW]);

                    Log::channel('filelog')
                        ->debug('File wasn\'t sent to FTP', ['id' => $fileQueue->id]);
                }
            }
        });

        return 0;
    }
}
