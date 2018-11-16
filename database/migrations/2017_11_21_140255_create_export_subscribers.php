<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExportSubscribers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::unprepared("CREATE PROCEDURE `export_subscribers_with_status`(
        	IN listID INT,
            IN flagStatus VARCHAR(255),
            IN header LONGTEXT,
            IN field LONGTEXT,
            IN fileName LONGTEXT,
            IN supperssedID INT
        )
        BEGIN
        	DECLARE tbl_subscriber LONGTEXT DEFAULT \"\";
            DECLARE tbl_supperssion LONGTEXT DEFAULT \"\";
            DECLARE string_field LONGTEXT DEFAULT \"\";
            
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
            
            SET tbl_subscriber = CONCAT(\"subscribers_l_\", listID);

            START TRANSACTION;
                SET field = CONCAT(field, \",\");
                WHILE (LOCATE(',', field) > 0) DO
    			   SET @item = SUBSTRING(field,1, LOCATE(',',field)-1);
    			   SET field = SUBSTRING(field, LOCATE(',',field) + 1);

    				IF LENGTH(string_field) = 0 AND @item = 'phone' THEN
						-- field phone
    					SET string_field = CONCAT(\"T1.\", @item, \" AS \", @item);
					ELSEIF LENGTH(string_field) = 0 AND @item != 'phone' THEN
						-- field phone encrypted
                        SET string_field = CONCAT(\"ENCRYPT_PHONE(T1.phone) AS phone\");
    				ELSE
    					SET string_field = CONCAT(string_field, \", IFNULL(T1.\", @item, \", '') AS \", @item);
    				END IF;

    			END WHILE;

        		IF flagStatus = \"SUPPERSSED\" THEN
                    SET tbl_supperssion = CONCAT(\"subscribers_l_\", supperssedID);

        			SET @qr = CONCAT(\"SELECT \", header , \" UNION SELECT \", string_field ,\" INTO OUTFILE '\", fileName ,\"' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '' LINES TERMINATED BY '\n' FROM \", tbl_subscriber , \" AS T1 INNER JOIN \", tbl_supperssion ,\" AS T2 ON T1.phone = T2.phone;\");
                    PREPARE qr FROM @qr;
        			EXECUTE qr;
        			DEALLOCATE PREPARE qr;
                ELSE
        			SET @qr = CONCAT(\"SELECT \", header , \" UNION SELECT \", string_field ,\" INTO OUTFILE '\", fileName ,\"' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '' LINES TERMINATED BY '\n' FROM \", tbl_subscriber , \" AS T1 WHERE status = '\", flagStatus ,\"';\");
                    PREPARE qr FROM @qr;
        			EXECUTE qr;
        			DEALLOCATE PREPARE qr;
                END IF;
            COMMIT;
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
        $sql = "DROP PROCEDURE IF EXISTS export_subscribers_with_status";
        DB::connection ()->getPdo ()->exec ( $sql );
    }
}
