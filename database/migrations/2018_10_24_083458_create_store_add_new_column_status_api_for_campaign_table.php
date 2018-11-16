<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreAddNewColumnStatusApiForCampaignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared ('CREATE PROCEDURE `add_new_column_is_api_for_campaign_table`()
            BEGIN
                        DECLARE bDone INT DEFAULT FALSE;
                        DECLARE tblName VARCHAR(255);
                        DECLARE curs CURSOR FOR SELECT DISTINCT table_name FROM information_schema.tables WHERE table_name LIKE \'campaign_u_%\';
                        DECLARE CONTINUE HANDLER FOR NOT FOUND SET bDone = TRUE;
                        
                        OPEN curs;
                            allTable: LOOP
                                FETCH curs INTO tblName;
            
                                IF bDone THEN
                                    LEAVE allTable;
                                END IF;
            
                                SET @query = CONCAT("ALTER TABLE ", tblName, " ADD COLUMN is_api BOOLEAN AFTER status ");
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
        $sql = "DROP PROCEDURE IF EXISTS add_new_column_is_api_for_campaign_table";
        DB::connection ()->getPdo ()->exec ( $sql );
    }
}
