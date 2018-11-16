<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrontabQueuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create ( 'crontab_queues', function (Blueprint $table) {
            $table->increments ( 'id' );
            $table->enum ( 'type', ['CAMPAIGN_SEND', 'CAMPAIGN_SENT_STATUS', 'CAMPAIGN_STATISTIC_REPORT'] );
            $table->unsignedInteger ( 'user_id' );
            $table->unsignedInteger ( 'data_id' );
            $table->timestamps ();
            // Index
            $table->index ( 'type' );
            $table->index ( 'user_id' );
            $table->index ( 'data_id' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists ( 'crontab_queues' );
    }
}
