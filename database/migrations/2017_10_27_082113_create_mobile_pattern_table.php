<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobilePatternTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // create queue template
        Schema::create ( 'mobile_pattern', function (Blueprint $table) {
            $table->increments ( 'id' );
            $table->string ( 'number_pattern', 15 );
            $table->string ( 'country', 5 )->nullable ();
            $table->string ( 'network', 100 )->nullable ();
            $table->unsignedInteger ( 'created_by' )->nullable ();
            $table->unsignedInteger ( 'updated_by' )->nullable ();
            $table->timestamps ();
            // Index
            $table->unique( 'number_pattern', 'unique_number_pattern' );
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
        Schema::dropIfExists('mobile_pattern');
    }
}
