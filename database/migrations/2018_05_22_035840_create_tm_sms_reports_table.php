<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmSmsReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tm_sms_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('return_message_id', 45);
            $table->string('return_from', 20);
            $table->string('return_to', 45);
            $table->text('return_message');
            $table->string('return_mccmnc', 15)->nullable();
            $table->double('return_price')->nullable()->default(0);
            $table->string('return_currency', 3)->nullable();
            $table->integer('return_sms_count')->nullable();
            $table->string('return_error_code', 3);
            $table->enum ('return_status', ['PENDING', 'DELIVERED', 'EXPIRED', 'FAILED'] )->default('PENDING');
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
        Schema::dropIfExists('tm_sms_reports');
    }
}
