<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMccmncTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // create mccmnc table
        Schema::create ( 'mccmnc', function (Blueprint $table) {
            $table->increments ( 'id' );
            $table->string ( 'mccmnc', 15 );
            $table->string ( 'country', 5 )->nullable ();
            $table->string ( 'network', 100 )->nullable ();
            $table->unsignedInteger ( 'created_by' )->nullable ();
            $table->unsignedInteger ( 'updated_by' )->nullable ();
            $table->timestamps ();
            // Index
            $table->unique('mccmnc', 'unique_mccmnc');
            $table->index ( 'country' );
            $table->index ( 'country', 'network' );
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
        Schema::dropIfExists('mccmnc');
    }
}
