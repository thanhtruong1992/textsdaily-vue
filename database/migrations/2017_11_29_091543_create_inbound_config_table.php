<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInboundConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create ( 'inbound_config', function (Blueprint $table) {
            $table->increments ( 'id' );
            $table->string ( 'number', 15 );
            $table->date('expiry_date');
            $table->unsignedInteger ( 'group2_user_id' )->nullable ();
            $table->unsignedInteger ( 'group3_user_id' )->nullable ();
            $table->string ( 'keyworks', 255 )->nullable ();
            $table->enum ( 'status', [
                    'ACTIVE',
                    'INACTIVE',
            ] )->default ( 'ACTIVE' );
            $table->unsignedInteger ( 'created_by' )->nullable ();
            $table->unsignedInteger ( 'updated_by' )->nullable ();
            $table->timestamps ();
            // Foreign key
            $table->foreign('group2_user_id')->references('id')->on('users')->onDelete('SET NULL');
            $table->foreign('group3_user_id')->references('id')->on('users')->onDelete('SET NULL');
            // Index
            $table->index ( 'number' );
            $table->index ( 'status' );
            $table->index ( 'expiry_date' );
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
        Schema::dropIfExists ( 'inbound_config' );
    }
}
