<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriberListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriber_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->string('description', 255)->nullable();
            $table->integer('user_id')->unsigned();
            $table->unsignedInteger('total_subscribers')->default(0);
            $table->boolean('is_global')->default(false);
            $table->enum ( 'detect_status', ['PENDING', 'PROCESSING', 'PROCESSED'] )->default ( 'PENDING' );
            $table->timestamp('detect_updated_at')->nullable ();
            $table->integer('created_by');
            $table->integer('updated_by');
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
        Schema::dropIfExists('subscriber_lists');
    }
}
