<?php

use App\Models\FileQueue;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FileQueueAddStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('file_queue', function (Blueprint $table){
            $table->string('status', 30)->default(FileQueue::STATUS_NEW)->after('file_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('file_queue', function(Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
