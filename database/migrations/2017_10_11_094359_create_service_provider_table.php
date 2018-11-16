<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceProviderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // create queue template
        Schema::create ( 'service_provider', function (Blueprint $table) {
            $table->increments ( 'id' );
            $table->string ( 'code', 25 );
            $table->string ( 'name', 45 );
            $table->string ( 'config_url' );
            $table->string ( 'config_username', 255 )->nullable ();
            $table->string ( 'config_password', 255 )->nullable ();
            $table->string ( 'config_access_key', 255 )->nullable ();
            $table->tinyInteger('default')->default(0);
            $table->enum ( 'status', ['ACTIVE', 'INACTIVE'] )->default ( 'ACTIVE' );
            $table->unsignedInteger ( 'created_by' )->nullable ();
            $table->unsignedInteger ( 'updated_by' )->nullable ();
            $table->timestamps ();
            // Index
            $table->index ( ['code', 'name'] );
            $table->index ( 'config_url' );
            $table->index ( 'default' );
            $table->index ( 'status' );
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
        Schema::dropIfExists ( 'service_provider' );
    }
}
