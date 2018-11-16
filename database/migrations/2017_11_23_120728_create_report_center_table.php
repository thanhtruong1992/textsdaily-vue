<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportCenterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_center', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->timestamp('from')->nullable();
            $table->timestamp('to')->nullable();
            $table->string('time_zone', 45)->nullable();
            $table->text('params')->nullable();
            $table->string("result", 255)->nullable();
            $table->text("notification_emails")->nullable();
            $table->enum('status', ['PENDING', 'PROCESSING', 'PROCESSED'])->default('PENDING');
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
        Schema::dropIfExists('report_center');
    }
}
