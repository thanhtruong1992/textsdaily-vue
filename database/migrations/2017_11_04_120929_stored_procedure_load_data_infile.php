<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StoredProcedureLoadDataInfile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::unprepared ( "CREATE PROCEDURE `loadDataInfileCSV`(
            	IN tempTableName VARCHAR (255),
                IN targetTableName VARCHAR (255),
                IN fieldInsert LONGTEXT, -- Ex: field1, field2
                IN fieldUpdate LONGTEXT -- Ex: field1 = VALUES(field1), field2 = VALUES(field2)
            )
            BEGIN

                DECLARE EXIT HANDLER FOR SQLEXCEPTION
            	BEGIN
            		-- ERROR
            		ROLLBACK;
            	END;

            	DECLARE EXIT HANDLER FOR SQLWARNING
            	BEGIN
            		-- WARNING
            	ROLLBACK;
            	END;

            	START TRANSACTION;

                    -- STEP 1: INSERT DATA FROM TEMPORARY TABLE TO TARGET TABLE
            		SET @query = CONCAT('INSERT INTO ', targetTableName, ' ( ', fieldInsert, ' ) SELECT ', fieldInsert, ' FROM ', tempTableName);
                    IF ( fieldUpdate IS NOT NULL ) THEN
            			SET @query = CONCAT( @query, ' ON DUPLICATE KEY UPDATE ', fieldUpdate , ';');
                    END IF;
            		PREPARE qr FROM @query;
            		EXECUTE qr;
            		DEALLOCATE PREPARE qr;

                    -- STEP 2: REMOVE TEMPORARY TABLE
                    SET @qr = CONCAT('DROP TABLE ', tempTableName, ';');
            	   PREPARE qr FROM @qr;
            	   EXECUTE qr;
            	   DEALLOCATE PREPARE qr;

            	COMMIT;
            END" );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        $sql = "DROP PROCEDURE IF EXISTS loadDataInfileCSV";
        DB::connection ()->getPdo ()->exec ( $sql );
    }
}
