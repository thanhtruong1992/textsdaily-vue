<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignStatsLinkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create ( 'campaign_stats_link_u_template', function (Blueprint $table) {
            $table->increments ( 'id' );
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('campaign_id');
            $table->unsignedInteger('link_id');
            $table->string('url');
            $table->string('location')->nullable();
            $table->string('ip')->nullable();
            $table->enum('status', ['PENDING', 'PROCESSING', 'PROCESSED'])->default('PENDING');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
            // Index
            $table->index('user_id');
            $table->index('campaign_id');
            $table->index('url');
            $table->index('location');
            $table->index('ip');
            $table->index('status');

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
        Schema::dropIfExists ( 'campaign_stats_link_u_template' );
    }
}
