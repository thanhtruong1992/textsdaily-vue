<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCampaignPaused extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_paused', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('campaign_id');
            $table->integer('queue_id');
            $table->integer('count')->default(1);
            $table->enum('tracking_status', ['PENDING', 'PROCESSING', 'PROCESSED'])->default('PENDING');
            $table->timestamp('tracking_updated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('camapaign_paused');
    }
}
