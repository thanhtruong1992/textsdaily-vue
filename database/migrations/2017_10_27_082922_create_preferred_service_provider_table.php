<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreferredServiceProviderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // create queue template
        Schema::create ( 'preferred_service_provider', function (Blueprint $table) {
            $table->increments ( 'id' );
            $table->string ( 'country', 5 )->nullable ();
            $table->string ( 'network', 100 )->nullable ();
            $table->string( 'service_provider', 25 )->nullable ();
            $table->unsignedInteger ( 'created_by' )->nullable ();
            $table->unsignedInteger ( 'updated_by' )->nullable ();
            $table->timestamps ();
            // Index
            $table->unique(['country', 'network']);
            // Foreign key
            $table->foreign('service_provider')->references('code')->on('service_provider');
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
        Schema::dropIfExists('preferred_service_provider');
    }
}
