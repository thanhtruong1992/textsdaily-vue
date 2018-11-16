<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreAutoTriggerReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::unprepared("CREATE PROCEDURE `auto_trigger_report`(
        	IN startDate VARCHAR(50),
            IN endDate VARCHAR(50),
            IN headerCSV LONGTEXT,
            IN pathFile LONGTEXT
        )
        BEGIN
        	DECLARE query_campaign LONGTEXT DEFAULT \"\";
            DECLARE x INT DEFAULT 1;
            DECLARE str_report LONGTEXT DEFAULT \"\";

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
        		SET @qr = CONCAT(\"SELECT GROUP_CONCAT(DISTINCT TABLE_NAME SEPARATOR ',') INTO @tbl_names FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE 'campaign_u_%' AND TABLE_NAME != 'campaign_u_template'\");
                PREPARE qr FROM @qr;
        		EXECUTE qr;
        		DEALLOCATE PREPARE qr;

                IF LENGTH(@tbl_names) > 0 THEN
        			SET @tbl_names = CONCAT(@tbl_names, \",\");

        			WHILE (LOCATE(',', @tbl_names) > 0) DO
        				SET @tbl_name = SUBSTRING(@tbl_names,1, LOCATE(',',@tbl_names)-1);
        				SET @tbl_names = SUBSTRING(@tbl_names, LOCATE(',',@tbl_names) + 1);
                        IF LENGTH(query_campaign) = 0 THEN
        					SET query_campaign = CONCAT(\"SELECT id AS campaign_id, user_id FROM \", @tbl_name, \" WHERE status = 'SENT' AND send_process_finished_on BETWEEN '\", startDate , \"' AND  '\", endDate,\"'\");
        				ELSE
        					SET query_campaign = CONCAT(query_campaign, \" UNION SELECT id AS campaign_id, user_id FROM \", @tbl_name, \" WHERE status = 'SENT' AND send_process_finished_on BETWEEN '\", startDate , \"' AND  '\", endDate,\"'\");
                        END IF;

        			END WHILE;

                    SET @qr = CONCAT(\"SELECT count(1) INTO @countCampaign FROM (\", query_campaign ,\") AS T1;\");
                    PREPARE qr FROM @qr;
        			EXECUTE qr;
        			DEALLOCATE PREPARE qr;

                    WHILE (x <= @countCampaign) DO
        				SET @qr = CONCAT(\"SELECT T1.user_id, T1.campaign_id INTO @userID, @campaignID FROM (SELECT *, @rownum := @rownum + 1 AS rank FROM (\", query_campaign ,\") AS T1, (SELECT @rownum := 0) r) AS T1 WHERE rank = \", x , \";\");
                        PREPARE qr FROM @qr;
        				EXECUTE qr;
        				DEALLOCATE PREPARE qr;

                        SET @tbl_report = CONCAT(\"report_list_summary_u_\", @userID);
                        IF LENGTH(str_report) = 0 THEN
        					SET str_report = CONCAT(\"SELECT '\", @tbl_report ,\"' AS table_report, service_provider, country, network, totals, agency_expenses, IFNULL(currency, '') AS currency FROM \", @tbl_report , \" WHERE campaign_id = \", @campaignID);
                        ELSE
        					SET str_report = CONCAT(str_report, \" UNION ALL SELECT '\", @tbl_report ,\"' AS table_report, service_provider, country, network, totals, agency_expenses, IFNULL(currency, '') AS currency FROM \", @tbl_report , \" WHERE campaign_id = \", @campaignID);
                        END IF;
                        SET x = x + 1;
                    END WHILE;

                    IF LENGTH(str_report) = 0 THEN
        				SET @qr = CONCAT(\"SELECT \", headerCSV ,\" INTO OUTFILE '\", pathFile ,\"' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '' LINES TERMINATED BY '\n';\");
                    ELSE
        				SET @qr = CONCAT(\"SELECT \", headerCSV ,\" UNION SELECT service_provider, IFNULL(country, '') AS country, IFNULL(network, '') AS network, SUM(totals) AS message_count, SUM(agency_expenses) AS total_cost, currency INTO OUTFILE '\", pathFile ,\"' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '' LINES TERMINATED BY '\n' FROM (\", str_report ,\") AS T1 GROUP BY country, network, service_provider;\");
                    END IF;

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
        $sql = "DROP PROCEDURE IF EXISTS auto_trigger_report";
        DB::connection ()->getPdo ()->exec ( $sql );
    }
}
