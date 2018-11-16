<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create ( 'campaign_recipients_u_template', function (Blueprint $table) {
            $table->increments ( 'id' );
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('campaign_id');
            $table->unsignedInteger('list_id');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();

            // Index
            $table->index('user_id');
            $table->index('campaign_id');
            $table->index('list_id');
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
        Schema::dropIfExists ( 'campaign_recipients_u_template' );
    }
}
