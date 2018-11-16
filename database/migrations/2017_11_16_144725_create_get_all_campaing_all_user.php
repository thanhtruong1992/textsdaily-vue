<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGetAllCampaingAllUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE PROCEDURE `get_all_data_campaign_all_user`()
        BEGIN
            DECLARE query_data BOOLEAN DEFAULT false;
            DECLARE string_query LONGTEXT DEFAULT \"\";

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

            SET @qr = CONCAT(\"SELECT DISTINCT GROUP_CONCAT(TABLE_NAME SEPARATOR ',') INTO @listTable FROM information_schema.tables where TABLE_NAME LIKE 'campaign_u_%' AND TABLE_NAME != 'campaign_u_template';\");
        	PREPARE qr FROM @qr;
        	EXECUTE qr;
        	DEALLOCATE PREPARE qr;

        	IF LENGTH(@listTable) > 0 THEN
        		SET @listTable = CONCAT(@listTable, \",\");

        		WHILE (LOCATE(',', @listTable) > 0) DO
        		   SET @tbl_name = SUBSTRING(@listTable,1, LOCATE(',',@listTable)-1);
        		   SET @listTable = SUBSTRING(@listTable, LOCATE(',',@listTable) + 1);

        			IF LENGTH(string_query) = 0 THEN
        				SET string_query = CONCAT(\"SELECT * FROM \", @tbl_name ,\" where backend_statistic_report = 'PENDING' AND STATUS = 'SENT'\");
        			ELSE
        				SET string_query = CONCAT(string_query, \" UNION SELECT * FROM \", @tbl_name ,\" where backend_statistic_report = 'PENDING' AND STATUS = 'SENT'\");
        			END IF;

        		END WHILE;
        	END IF;

            START TRANSACTION;
        		IF LENGTH(string_query) > 0 THEN
        			SET string_query = CONCAT(string_query, \" ORDER BY backend_statistic_report_updated_at ASC LIMIT 0,1;\");

        			SET @qr = CONCAT(string_query);
        			PREPARE qr FROM @qr;
        			EXECUTE qr;
        			DEALLOCATE PREPARE qr;
        		ELSE
        			SELECT query_data;
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
        $sql = "DROP PROCEDURE IF EXISTS get_all_data_campaign_all_user";
        DB::connection ()->getPdo ()->exec ( $sql );
    }
}
