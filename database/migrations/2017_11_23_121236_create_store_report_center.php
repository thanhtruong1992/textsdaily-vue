<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreReportCenter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::unprepared("CREATE PROCEDURE `report_centers`(
        	IN listUser LONGTEXT,
            IN dateFrom VARCHAR(50),
            IN dateTo VARCHAR(50),
            IN campaignName VARCHAR(255),
            IN headers LONGTEXT,
            IN stringFields LONGTEXT,
            IN typeUser VARCHAR(10),
            IN timezone VARCHAR(50),
            IN pathFile LONGTEXT
        )
        BEGIN
            DECLARE str_query LONGTEXT DEFAULT \"\";
            DECLARE query_user LONGTEXT DEFAULT \"\";
            DECLARE all_query LONGTEXT DEFAULT \"\";

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

            IF LENGTH(listUser) > 0 THEN
        		SET listUser = CONCAT(listUser, \",\");

        		WHILE (LOCATE(',', listUser) > 0) DO
        			SET @user_id = SUBSTRING(listUser,1, LOCATE(',',listUser)-1);
        			SET listUser = SUBSTRING(listUser, LOCATE(',',listUser) + 1);
        			SET @tbl_campaign = CONCAT(\"campaign_u_\", @user_id);
                    SET str_query = CONCAT(\" WHERE status = 'sent'\");

                    IF typeUser = \"GROUP4\" THEN
        				SET query_user = CONCAT(\"SELECT reader_id FROM users WHERE id = \", @user_id);
        			ELSEIF typeUser = \"GROUP1\" THEN
        				SET query_user = CONCAT(\"SELECT parent_id FROM users WHERE id = \", @user_id);
        			ELSE
                        SET query_user = CONCAT(\"SELECT id FROM users WHERE id = \", @user_id);
        			END IF;

                    IF LENGTH(campaignName) > 0 THEN
        				SET str_query = CONCAT(str_query, \" AND name like '%\", campaignName ,\"%'\");
                    END IF;

                    IF LENGTH(dateFrom) > 0 && LENGTH(dateTo) > 0 THEN
        				SET str_query = CONCAT(str_query, \" AND (CONVERT_TZ(send_time, send_timezone, 'UTC') BETWEEN '\", dateFrom ,\"' AND '\", dateTo ,\"');\");
                    ELSEIF LENGTH(dateFrom) > 0 THEN
        				SET str_query = CONCAT(str_query, \" AND CONVERT_TZ(send_time, send_timezone, 'UTC') >= '\", dateFrom ,\"';\");
                    ELSEIF LENGTH(dateTo) > 0 THEN
        				SET str_query = CONCAT(str_query, \" AND CONVERT_TZ(send_time, send_timezone, 'UTC') <= '\", dateTo ,\"';\");
                    END IF;

         			SET @qr = CONCAT(\"SELECT GROUP_CONCAT(id SEPARATOR ',') INTO @listCampaign FROM \", @tbl_campaign, str_query);
        			PREPARE qr FROM @qr;
        			EXECUTE qr;
        			DEALLOCATE PREPARE qr;

        			SET @listCampaign = CONCAT(@listCampaign, \",\");

        			WHILE (LOCATE(',', @listCampaign) > 0) DO
        				SET @campaign_id = SUBSTRING(@listCampaign,1, LOCATE(',',@listCampaign)-1);
        				SET @listCampaign = SUBSTRING(@listCampaign, LOCATE(',',@listCampaign) + 1);
        				SET @tbl_queue = CONCAT(\"queue_u_\", @user_id, \"_c_\", @campaign_id);
                        SET @query_queue = CONCAT(\"SELECT T1.*, \", @campaign_id ,\" AS campaign_id, IFNULL(T2.sender, '') AS sender, IFNULL(T3.currency, '') AS currency, IFNULL(T3.name, '') AS client_name  FROM (SELECT T1.return_message_id, T1.phone, COALESCE(T2.name, T1.country, '') AS country, IFNULL(T1.network, '') AS network, T1.service_provider, T1.sum_price_client, IF(T1.ported = 0, 'NO', 'YES') as ported, T1.message_count, IFNULL(DATE_FORMAT(CONVERT_TZ(T1.report_updated_at, 'UTC', '\", timezone ,\"'), '%M %d %Y %h:%i %p'), '') AS report_updated_at,  IFNULL(DATE_FORMAT(CONVERT_TZ(T1.updated_at, 'UTC', '\", timezone ,\"'), '%M %d %Y %h:%i %p'), '') AS updated_at, IFNULL(CONVERT_TZ(T1.updated_at, 'UTC', '\", timezone ,\"'), '') AS updated_at_sort,IFNULL(T1.return_status, '') AS return_status, IF(T1.return_status = 'FAILED', T1.return_status_message, null) AS return_status_message, IFNULL(REPLACE(REPLACE(REPLACE(T1.message, '\n', ' '), '\r', ' '), '\r\n', ' '), '') AS message, (\",query_user,\") AS user_id FROM \",@tbl_queue,\" AS T1 LEFT JOIN countries T2 ON T1.country = T2.code ) AS T1 LEFT JOIN \",@tbl_campaign,\" AS T2 ON \", @campaign_id ,\" = T2.id LEFT JOIN users AS T3 ON T3.id = T1.user_id\");
                        SET @tempField = CONCAT(stringFields, \", updated_at_sort \");
                        IF LENGTH(all_query) = 0 THEN
        					SET all_query = CONCAT(\"SELECT \", @tempField , \" FROM (\", @query_queue ,\") AS T1 \");
                        ELSE
        					SET all_query = CONCAT(all_query, \" UNION ALL SELECT \", @tempField , \" FROM (\", @query_queue ,\") AS T2 \");
                        END IF;
                    END WHILE;
        		END WHILE;

                IF LENGTH(all_query) = 0 THEN
                    SET @qr = CONCAT(\"SELECT \", headers , \" INTO OUTFILE '\", pathFile ,\"' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '' LINES TERMINATED BY '\n';\");
                ELSE
                    SET @qr = CONCAT(\"SELECT \", headers , \" UNION ALL SELECT \", stringFields, \" INTO OUTFILE '\", pathFile ,\"' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\\\"\' ESCAPED BY '\\\"\' LINES TERMINATED BY '\n' FROM (\",all_query,\" ORDER BY updated_at_sort DESC, sender ASC) AS T1;\");
                END IF;

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
        $sql = "DROP PROCEDURE IF EXISTS export_subscribers_with_status";
        DB::connection ()->getPdo ()->exec ( $sql );
    }
}
