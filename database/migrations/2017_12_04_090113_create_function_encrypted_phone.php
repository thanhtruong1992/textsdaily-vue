<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFunctionEncryptedPhone extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::unprepared ( "CREATE  FUNCTION `ENCRYPT_PHONE`(
        	phone VARCHAR(20)
        ) RETURNS LONGTEXT CHARSET latin1
        BEGIN
        	RETURN CONCAT(SUBSTR(phone, 1, 3), REGEXP_REPLACE(SUBSTR(phone, 4, LENGTH(phone)), '[0-9]', '*'));
        END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        $sql1 = "DROP FUNCTION IF EXISTS ENCRYPT_PHONE";
        DB::connection ()->getPdo ()->exec ( $sql1 );
    }
}
