<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreProcedureMoveDataQueueIntoReportNew extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::unprepared ( "CREATE PROCEDURE `move_data_queue_into_report_new`()
        BEGIN
        	DECLARE done INT DEFAULT FALSE;
            DECLARE tbl_campaign VARCHAR(255);
            DECLARE queryCampaign LONGTEXT;
            DECLARE firstDataUserID, firstDataCampaignID INT DEFAULT NULL;
            DECLARE totalPending INT DEFAULT 0;
            DECLARE tbl_queue VARCHAR(255) DEFAULT \"\";

        	DECLARE curCampaign CURSOR FOR SELECT DISTINCT table_name FROM information_schema.tables WHERE table_name LIKE 'campaign_u_%' AND table_name <> 'campaign_u_template';
        	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

            #1. CHECK DATA IN crontab_queues
            SELECT count(1) INTO @countQueues FROM crontab_queues WHERE `type` = 'CAMPAIGN_STATISTIC_REPORT';

            #2. COLLECT DATA FROM crontab_queues IF NULL
            IF @countQueues = 0 THEN
        		OPEN curCampaign;

        			campaign_loop: LOOP
        				FETCH curCampaign INTO tbl_campaign;
        				IF done THEN
        					LEAVE campaign_loop;
        				END IF;

        				IF queryCampaign IS NULL THEN
        					SET queryCampaign = CONCAT(\"SELECT 'CAMPAIGN_STATISTIC_REPORT' AS type, user_id AS user_id, id AS data_id, backend_statistic_report_updated_at FROM \", tbl_campaign , \" WHERE status = 'SENT' AND backend_statistic_report = 'PENDING'\");
        				ELSE
        					SET queryCampaign = CONCAT(queryCampaign, \" UNION SELECT 'CAMPAIGN_STATISTIC_REPORT' AS type, user_id AS user_id, id AS data_id, backend_statistic_report_updated_at FROM \", tbl_campaign , \" WHERE status = 'SENT' AND backend_statistic_report = 'PENDING'\");
        				END IF;
        			END LOOP;

        		CLOSE curCampaign;

        		#2.1. INSERT DATA TO crontab_queues
        		SET @qr = CONCAT(\"INSERT INTO crontab_queues(`type`, `user_id`, `data_id`, `created_at`) (SELECT `type`, `user_id`, `data_id`, '\", now() ,\"' FROM (\", queryCampaign, \") AS tmp ORDER BY backend_statistic_report_updated_at ASC);\");
        		PREPARE qr FROM @qr;
        		EXECUTE qr;
        		DEALLOCATE PREPARE qr;
            END IF;

            #3. GET FIRT ID CAMPAIGN
            SELECT id, user_id, data_id INTO @id, firstDataUserID, firstDataCampaignID FROM crontab_queues WHERE `type`= 'CAMPAIGN_STATISTIC_REPORT' LIMIT 0,1;

            -- TABLE QUEUE
            SET tbl_queue = CONCAT(\"queue_u_\", firstDataUserID, \"_c_\", firstDataCampaignID);

            #4. DELETE FIRST ID CAMPAIGN
            DELETE FROM crontab_queues WHERE `id` = @id;

            #5. UPDATE backend_statistic_report = PROCESSING
            SET @qr = CONCAT(\"UPDATE campaign_u_\", firstDataUserID, \" SET backend_statistic_report = 'PROCESSING', backend_statistic_report_updated_at = '\", now() ,\"' WHERE id = \", firstDataCampaignID, \";\");
        	PREPARE qr FROM @qr;
        	EXECUTE qr;
        	DEALLOCATE PREPARE qr;

            #6. GET TOTAL DATA PENDING OF TABLE QUEUE
            SET @qr = CONCAT(\"SELECT count(1) INTO @totalPending FROM \", tbl_queue ,\" WHERE return_status = 'PENDING';\");
        	PREPARE qr FROM @qr;
        	EXECUTE qr;
        	DEALLOCATE PREPARE qr;

            #7. REMOVE DATA REPORT
            SET @qr = CONCAT(\"DELETE FROM report_list_summary_u_\", firstDataUserID ,\" WHERE `campaign_id` = \", firstDataCampaignID ,\";\");
        	PREPARE qr FROM @qr;
        	EXECUTE qr;
        	DEALLOCATE PREPARE qr;

            #8. MOVE DATA TABLE QUEUE INTO TABLE REPORT
            SET @queue = CONCAT(\"SELECT \", firstDataCampaignID ,\" AS campaign_id, list_id, country, network, service_provider, return_currency, expenses, agency_expenses, client_expenses, SUM(totals) AS totals, SUM(pending) AS pending, SUM(delivered) AS delivered, SUM(failed) AS failed, SUM(expired) AS expired FROM (
                						SELECT list_id, service_provider, return_currency, country, network, SUM(message_count) AS totals, ROUND(SUM(`sum_price_client`), 2) AS expenses, ROUND(SUM(`return_price`), 2) AS agency_expenses, ROUND(SUM(`sum_price_agency`), 2) AS client_expenses, 0 AS pending, 0 AS delivered, 0 AS failed, 0 AS expired FROM \", tbl_queue ,\" GROUP BY country, network, service_provider, return_currency, list_id
                						UNION
                						SELECT list_id, service_provider, return_currency, country, network, 0 AS totals, 0 AS expenses, 0 AS agency_expenses, 0 AS client_expenses, SUM(message_count) AS pending, 0 AS delivered, 0 AS failed, 0 AS expired FROM \", tbl_queue ,\" WHERE return_status = 'PENDING' GROUP BY country, network, service_provider, return_currency, list_id
                						UNION
                						SELECT list_id, service_provider, return_currency, country, network, 0 AS totals, 0 AS expenses, 0 AS agency_expenses, 0 AS client_expenses, 0 AS pending, SUM(message_count) AS delivered, 0 AS failed, 0 AS expired FROM \", tbl_queue ,\" WHERE return_status = 'DELIVERED' GROUP BY country, network, service_provider, return_currency, list_id
                						UNION
                						SELECT list_id, service_provider, return_currency, country, network, 0 AS totals, 0 AS expenses, 0 AS agency_expenses, 0 AS client_expenses, 0 AS pending, 0 AS delivered, SUM(message_count) AS failed, 0 AS expired FROM \", tbl_queue ,\" WHERE return_status = 'FAILED' GROUP BY country, network, service_provider, return_currency, list_id
                						UNION
                						SELECT list_id, service_provider, return_currency, country, network, 0 AS totals, 0 AS expenses, 0 AS agency_expenses, 0 AS client_expenses, 0 AS pending, 0 AS delivered, 0 AS failed, SUM(message_count) AS expired FROM \", tbl_queue ,\" WHERE return_status = 'EXPIRED' GROUP BY country, network, service_provider, return_currency, list_id) AS T1
                						GROUP BY T1.country, T1.network, T1.service_provider, T1.return_currency, T1.list_id\"
                					 );
        	SET @qr = CONCAT(\"INSERT INTO report_list_summary_u_\", firstDataUserID ,\" (campaign_id, list_id, service_provider, currency, country, network, expenses, agency_expenses, client_expenses, totals, pending, delivered, failed, expired, created_at, updated_at)
        					SELECT campaign_id, list_id, service_provider, return_currency, country, network, expenses, agency_expenses, client_expenses, totals, pending, delivered, failed, expired, '\", NOW() ,\"', '\", NOW() ,\"' FROM (\", @queue ,\") AS TEMP;\"
        				);
        	PREPARE qr FROM @qr;
        	EXECUTE qr;
        	DEALLOCATE PREPARE qr;

            #8. DETECT @totalPending of table queue
            IF @totalPending = 0 THEN
        		#8.1. UPDATE backend_statistic_report = PROCESSED
                SET @qr = CONCAT(\"UPDATE campaign_u_\", firstDataUserID, \" SET backend_statistic_report = 'PROCESSED', backend_statistic_report_updated_at = '\", now() ,\"' WHERE id = \", firstDataCampaignID, \";\");
        		PREPARE qr FROM @qr;
        		EXECUTE qr;
        		DEALLOCATE PREPARE qr;
            ELSE
        		#8.2. UPDATE backend_statistic_report = PENDING
                SET @qr = CONCAT(\"UPDATE campaign_u_\", firstDataUserID, \" SET backend_statistic_report = 'PENDING', backend_statistic_report_updated_at = '\", now() ,\"' WHERE id = \", firstDataCampaignID, \";\");
        		PREPARE qr FROM @qr;
        		EXECUTE qr;
        		DEALLOCATE PREPARE qr;
            END IF;
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
        $sql1 = "DROP PROCEDURE IF EXISTS move_data_queue_into_report_new";
        DB::connection ()->getPdo ()->exec ( $sql1 );
    }
}
