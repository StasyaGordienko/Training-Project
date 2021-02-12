<?php

namespace App\Jobs;

use App\Helpers\Transport;
use App\Models\File;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSendingFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $file;
    protected $content;


    /**
     * Create a new job instance.
     *
     * @param File $file
     */
    public function __construct(File $file, string $content)
    {
        $this->file = $file;
        $this->content = $content;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::channel('filelog')->debug('Job begins');
        $isSent = Transport::sendFile($this->file, $this->content);
        if ($isSent) {

            $getFile = File::query()->where('file_hash', '=', $this->file->file_hash)->first();
            if ($getFile) {
                $getFile->status = File::STATUS_SENT;
                $getFile->save();
            }else{
                Log::channel('filelog')
                    ->debug('File status wasn\'t changed', ['file_hash' => $this->file->file_hash]);
            }
        } else {
            Log::channel('filelog')
                ->debug('File wasn\'t sent to FTP', ['id' => $this->file->file_hash]);
        }
        Log::channel('filelog')->debug('Job ends');
        return 0;
    }
}
