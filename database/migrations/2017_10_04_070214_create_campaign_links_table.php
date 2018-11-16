<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create ( 'campaign_links_u_template', function (Blueprint $table) {
            $table->increments ( 'id' );
            $table->unsignedInteger('campaign_id')->nullable();
            $table->text('url');
            $table->string('short_link')->nullable();
            $table->unsignedInteger('total_clicks')->default(0);
            $table->unsignedInteger('unique_clicks')->default(0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();

            // Index
            // $table-> index('url');
            // $table-> index('total_clicks');
            // $table-> index('unique_clicks');

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
        Schema::dropIfExists ( 'campaign_links_u_template' );
    }
}
