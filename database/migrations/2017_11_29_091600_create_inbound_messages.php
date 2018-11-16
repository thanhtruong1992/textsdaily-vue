<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInboundMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create ( 'inbound_messages_u_template', function (Blueprint $table) {
            $table->increments ( 'id' );
            $table->string ( 'from', 25 );
            $table->string ( 'to', 15 );
            $table->text('message')->nullable ();
            $table->unsignedInteger ( 'user_id' )->nullable ();
            $table->string ( 'keyworks' )->nullable ();
            $table->string ( 'message_id' )->nullable ();
            $table->text('return_data')->nullable ();
            $table->timestamps ();
            // Index
            $table->index ( 'from' );
            $table->index ( 'to' );
            $table->index ( 'keyworks' );
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
        Schema::dropIfExists ( 'inbound_messages_u_template' );
    }
}
