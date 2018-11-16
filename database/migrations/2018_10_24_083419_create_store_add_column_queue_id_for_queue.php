<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreAddColumnQueueIdForQueue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared ('CREATE PROCEDURE `add_column_queue_id_for_queue`()
            BEGIN
                DECLARE bDone INT DEFAULT FALSE;
                DECLARE tblName VARCHAR(255);
            
                DECLARE curs CURSOR FOR SELECT DISTINCT table_name FROM information_schema.tables WHERE table_name LIKE \'queue_u_%\';
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET bDone = TRUE;
            
                OPEN curs;
                    allTable: LOOP
                        FETCH curs INTO tblName;
            
                        IF bDone THEN
                            LEAVE allTable;
                        END IF;

                        SET @query = CONCAT("ALTER TABLE ", tblName ," ADD COLUMN report_status ENUM(\'PENDING\', \'REPORTING\', \'REPORTED\') DEFAULT \'PENDING\' AFTER status, ADD COLUMN queue_id VARCHAR(50) AFTER id,  ADD COLUMN sender VARCHAR(191) AFTER phone ");
                        PREPARE qr FROM @query;
                        EXECUTE qr;
                        DEALLOCATE PREPARE qr;
            
                    END LOOP;
                CLOSE curs;
            
            
            END
            
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = "DROP PROCEDURE IF EXISTS add_column_queue_id_for_queue";
        DB::connection ()->getPdo ()->exec ( $sql );
    }
}
