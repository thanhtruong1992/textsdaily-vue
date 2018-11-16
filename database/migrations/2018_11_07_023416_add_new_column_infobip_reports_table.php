<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnInfobipReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('infobip_reports', function (Blueprint $table) {
        //     $table->string('return_from', 20);
        //     $table->string('return_to', 45);
        //     $table->text('return_message');
        //     $table->string('return_mccmnc', 15)->nullable();
        //     $table->double('return_price')->nullable()->default(0);
        //     $table->string('return_currency', 3)->nullable();
        //     $table->integer('return_sms_count')->nullable();
        //     $table->string('return_error_code', 3)->nullable();
        //     $table->enum ('return_status', ['PENDING', 'DELIVERED', 'EXPIRED', 'FAILED'] )->default('PENDING');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
