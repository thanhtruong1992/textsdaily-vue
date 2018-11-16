<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventMoveDataQueueIntoReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::unprepared ( "CREATE EVENT event_move_data_queue_into_report
    ON SCHEDULE EVERY 1 MINUTE
    DO
      CALL move_data_queue_into_report_new();");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        $sql1 = "DROP EVENT IF EXISTS move_data_queue_into_report";
        DB::connection ()->getPdo ()->exec ( $sql1 );
    }
}
