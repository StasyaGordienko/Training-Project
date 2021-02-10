<?php

namespace App\Console\Commands;

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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //Log::debug('hhblh', ['1'=>'kjdnc']);
        $filesBuilder = FileQueue::query()
            ->where('delay', '<=', DB::raw('timestampdiff(minute,created_at,now())'));

        $filesBuilder->chunk(10, function ($fileQueueCollection) {
            foreach ($fileQueueCollection as $fileQueue) {

                $readyForSend = FileQueue::query()
                    ->where('id',$fileQueue->id)
                    ->where('status', FileQueue::STATUS_NEW)
                    ->update(['status' => FileQueue::STATUS_PROCESSING]);

                if (!$readyForSend) {
                    return false;
                }
                Transport::sendFile($fileQueue);
                $fileQueue->delete();

            }
        });

        return 0;
    }
}
