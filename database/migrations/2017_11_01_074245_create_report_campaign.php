<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportCampaign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('CREATE PROCEDURE `report_campaign_chart`(
        	IN userID INT,
            IN campaignID INT
        )
        BEGIN
        	declare tmp_table_name VARCHAR(255) DEFAULT "report_list_summary_u_";
            
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

        	IF(userID is not null) THEN
        		SET tmp_table_name = CONCAT(tmp_table_name, userID);
        	END IF;

        	START TRANSACTION;
                SET @qr = CONCAT("SELECT SUM(`total`) AS total, country, SUM(`delivered`) AS delivered FROM( SELECT SUM(`expenses`) AS total, IF(country = \'\', NULL, country) AS country, SUM(`delivered`) AS delivered FROM ", tmp_table_name, " WHERE campaign_id = ", campaignID , " GROUP BY country) AS T GROUP BY country ORDER BY total DESC, country ASC LIMIT 0, 5;");
        		PREPARE qr FROM @qr;
        		EXECUTE qr;
        		DEALLOCATE PREPARE qr;
        	COMMIT;
        END');

        DB::unprepared("CREATE PROCEDURE `report_campaign_data`(
        	IN userID INT,
            IN campaignID INT,
            IN listCountry LONGTEXT
        )
        BEGIN
        	declare tmp_table_name VARCHAR(255) DEFAULT 'report_list_summary_u_';
            
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

        	IF(userID is not null) THEN
        		SET tmp_table_name = CONCAT(tmp_table_name, userID);
        	END IF;

        	START TRANSACTION;
        		SET @qr = CONCAT('SELECT SUM(`expenses`) AS total_price, country, network, SUM(pending) AS pending, SUM(totals) AS totals, SUM(failed) AS failed, SUM(delivered) AS delivered, SUM(failed) AS failed, SUM(expired) AS expired, expenses FROM ', tmp_table_name, ' WHERE campaign_id = ', campaignID, listCountry, ' GROUP BY network, country ORDER BY country ASC , network ASC, total_price DESC');
        		PREPARE qr FROM @qr;
        		EXECUTE qr;
        		DEALLOCATE PREPARE qr;
        	COMMIT;
        END");

        DB::unprepared('CREATE PROCEDURE `get_data_short_link`(
        	IN userID INT,
            IN campaignID INT,
            IN delivered INT
        )
        BEGIN
        	DECLARE tbl_name VARCHAR(255) DEFAULT "";
            
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
            
            IF (userID IS NOT NULL) THEN
        		SET tbl_name = CONCAT("campaign_links_u_", userID);
            END IF;

            START TRANSACTION;
                SET @qr = CONCAT("SELECT count(1) INTO @delivered FROM queue_u_", userID,"_c_", campaignID ," WHERE return_message_id IS NOT NULL;");
                PREPARE qr FROM @qr;
        		EXECUTE qr;
        		DEALLOCATE PREPARE qr;
                
        		SET @qr = CONCAT("SELECT id, url, short_link, total_clicks, ", @delivered , " AS delivered, IFNULL(ROUND(total_clicks / ", @delivered ,", 0), \'0%\') AS clicked_rate FROM ", tbl_name ," WHERE campaign_id = ", campaignID ,";");
                PREPARE qr FROM @qr;
        		EXECUTE qr;
        		DEALLOCATE PREPARE qr;
            COMMIT;
        END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    	$sql = "DROP PROCEDURE IF EXISTS report_campaign_chart";
    	DB::connection ()->getPdo ()->exec ( $sql );

    	$sql2 = "DROP PROCEDURE IF EXISTS report_campaign_data";
    	DB::connection ()->getPdo ()->exec ( $sql2 );

    	$sql3 = "DROP PROCEDURE IF EXISTS get_data_short_link";
    	DB::connection ()->getPdo ()->exec ( $sql3 );
    }
}
