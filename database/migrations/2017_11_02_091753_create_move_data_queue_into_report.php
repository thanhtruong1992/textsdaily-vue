<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMoveDataQueueIntoReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('CREATE PROCEDURE `move_data_queue_into_report`(
        	IN userID INT,
            IN campaignID INT
        )
        BEGIN
        	DECLARE tbl_queue VARCHAR(255) DEFAULT "";
            DECLARE tbl_report VARCHAR(255) DEFAULT "";
            DECLARE field_price VARCHAR(255) DEFAULT "";
            
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

            SET tbl_queue = CONCAT("queue_u_", userID , "_c_", campaignID);
            SET tbl_report = CONCAT("report_list_summary_u_", userID);

            START TRANSACTION;
        		-- STEP total sent of campaign
        		SET @queue = CONCAT("SELECT ", campaignID ," AS campaign_id, list_id, country, network, service_provider, return_currency, expenses, agency_expenses, client_expenses, SUM(totals) AS totals, SUM(pending) AS pending, SUM(delivered) AS delivered, SUM(failed) AS failed, SUM(expired) AS expired FROM (
        						SELECT list_id, service_provider, return_currency, country, network, SUM(message_count) AS totals, ROUND(SUM(`sum_price_client`), 2) AS expenses, ROUND(SUM(`return_price`), 2) AS agency_expenses, ROUND(SUM(`sum_price_agency`), 2) AS client_expenses, 0 AS pending, 0 AS delivered, 0 AS failed, 0 AS expired FROM ", tbl_queue ," GROUP BY country, network, service_provider, return_currency, list_id
        						UNION
        						SELECT list_id, service_provider, return_currency, country, network, 0 AS totals, 0 AS expenses, 0 AS agency_expenses, 0 AS client_expenses, SUM(message_count) AS pending, 0 AS delivered, 0 AS failed, 0 AS expired FROM ", tbl_queue ," WHERE return_status = \'PENDING\' GROUP BY country, network, service_provider, return_currency, list_id
        						UNION
        						SELECT list_id, service_provider, return_currency, country, network, 0 AS totals, 0 AS expenses, 0 AS agency_expenses, 0 AS client_expenses, 0 AS pending, SUM(message_count) AS delivered, 0 AS failed, 0 AS expired FROM ", tbl_queue ," WHERE return_status = \'DELIVERED\' GROUP BY country, network, service_provider, return_currency, list_id
        						UNION
        						SELECT list_id, service_provider, return_currency, country, network, 0 AS totals, 0 AS expenses, 0 AS agency_expenses, 0 AS client_expenses, 0 AS pending, 0 AS delivered, SUM(message_count) AS failed, 0 AS expired FROM ", tbl_queue ," WHERE return_status = \'FAILED\' GROUP BY country, network, service_provider, return_currency, list_id
        						UNION
        						SELECT list_id, service_provider, return_currency, country, network, 0 AS totals, 0 AS expenses, 0 AS agency_expenses, 0 AS client_expenses, 0 AS pending, 0 AS delivered, 0 AS failed, SUM(message_count) AS expired FROM ", tbl_queue ," WHERE return_status = \'EXPIRED\' GROUP BY country, network, service_provider, return_currency, list_id) AS T1
        						GROUP BY T1.country, T1.network, T1.service_provider, T1.return_currency, T1.list_id"
        					 );
                SET @qr = CONCAT("INSERT INTO report_list_summary_u_", userID ," (campaign_id, list_id, service_provider, currency, country, network, expenses, agency_expenses, client_expenses, totals, pending, delivered, failed, expired, created_at, updated_at)
        						SELECT campaign_id, list_id, service_provider, return_currency, country, network, expenses, agency_expenses, client_expenses, totals, pending, delivered, failed, expired, \'", NOW() ,"\', \'", NOW() ,"\' FROM (", @queue ,") AS TEMP;"
        					);
                PREPARE qr FROM @qr;
        		EXECUTE qr;
        		DEALLOCATE PREPARE qr;

            COMMIT;
        END');

        DB::unprepared('CREATE PROCEDURE `total_pending_queue_campaign`(
        	IN userID INT,
            IN campaignID INT
        )
        BEGIN
        	DECLARE tbl_queue VARCHAR(255) DEFAULT "";
            
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

            SET tbl_queue = CONCAT("queue_u_", userID , "_c_", campaignID);

            START TRANSACTION;
                -- STEP total pending
                SET @qr = CONCAT("SELECT count(1) INTO @TotalPending FROM ", tbl_queue, " WHERE return_status = \'PENDING\';");
                PREPARE qr FROM @qr;
        		EXECUTE qr;
        		DEALLOCATE PREPARE qr;

                SELECT @TotalPending AS TotalPending;
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
        $sql2 = "DROP PROCEDURE IF EXISTS total_pending_queue_campaign";
        DB::connection ()->getPdo ()->exec ( $sql2 );

        $sql2 = "DROP PROCEDURE IF EXISTS move_data_queue_into_report";
        DB::connection ()->getPdo ()->exec ( $sql2 );
    }
}
