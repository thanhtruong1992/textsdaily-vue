<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExportCampaign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::unprepared ('CREATE PROCEDURE `export_campaign`(
        	IN campaignID INT,
            IN userID INT,
            IN pathFile LONGTEXT,
            IN defaultCurrency VARCHAR(255),
            IN detailed BOOLEAN,
            IN pending BOOLEAN,
            IN delivered BOOLEAN,
            IN expired BOOLEAN,
            IN failed BOOLEAN,
            IN encrypted BOOLEAN,
            IN timeZone LONGTEXT,
            IN typeUser VARCHAR(10)
        )
BEGIN
        	DECLARE x INT default 0;
        	DECLARE custom_field LONGTEXT DEFAULT "";
            DECLARE first_name VARCHAR(255) DEFAULT "";
            DECLARE last_name VARCHAR (255) DEFAULT "";
            DECLARE header_export LONGTEXT DEFAULT "\'MobileNumber\' AS phone,\'FirstName\' AS first_name,\'LastName\' AS last_name";
            DECLARE left_join LONGTEXT DEFAULT "";
            DECLARE string_custom_field LONGTEXT DEFAULT "";
            DECLARE header_export_detail LONGTEXT DEFAULT "\'External message id\' AS return_message_id,\'Destination number\' AS phone,\'Sender\' AS sender,\'Destination country\' AS country,\'Destination network\' AS network,\'Price per message\' AS sum_price_client,\'Currency\' AS currency,\'Ported\' AS ported,\'SMS count\' AS message_count,\'Sent time\' AS sent_time,\'Delivery report time\' AS report_updated_at,\'Status\' AS return_status,\'Status Message\' AS return_status_message,\'Message text\' AS message";

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
        		SET @qr = CONCAT("SELECT GROUP_CONCAT(list_id SEPARATOR \',\') INTO @listIDs FROM campaign_recipients_u_", userID , " WHERE campaign_id = ", campaignID ,";");
        		PREPARE qr FROM @qr;
        		EXECUTE qr;
        		DEALLOCATE PREPARE qr;

                SET @qr = CONCAT("SELECT count(1) INTO @countCustomField FROM (SELECT field_name FROM custom_fields WHERE list_id IN (", @listIDs ,") GROUP BY field_name) AS TEMP");
        		PREPARE qr FROM @qr;
        		EXECUTE qr;
        		DEALLOCATE PREPARE qr;

                -- string field first_name and last_name when select
                SET @count = (LENGTH(@listIDs) - LENGTH(REPLACE(@listIDs, ",", "")))/LENGTH(",") + 1;
        		SET @count = ROUND(@count, 0);
                loop_insert: LOOP
        			SET x = x + 1;
                    SET @list_id = TRIM(REPLACE(SUBSTRING(SUBSTRING_INDEX(@listIDs, ",", x), LENGTH(SUBSTRING_INDEX(@listIDs, ",", x-1)) + 1), ",", ""));

        			IF LENGTH(first_name) = 0 THEN
        				SET first_name = CONCAT("IFNULL(COALESCE(T", x ,".first_name");
        			ELSE
        				SET first_name = CONCAT(first_name, ", T", x ,".first_name");
                    END IF;

                    IF LENGTH(last_name) = 0 THEN
        				SET last_name = CONCAT("IFNULL(COALESCE(T", x ,".last_name");
        			ELSE
        				SET last_name = CONCAT(last_name, ", T", x ,".last_name");
                    END IF;

                    -- query left join
        			SET left_join = CONCAT(left_join, " LEFT JOIN subscribers_l_", @list_id, " AS T", x, " ON T", x, ".phone = queue.phone");

        			IF( x = @count) THEN
        				SET first_name = CONCAT(first_name, "), \'\') AS first_name");
        				SET last_name = CONCAT(last_name, "), \'\') AS last_name");
        				LEAVE loop_insert;
        			END IF;
        		END LOOP loop_insert;

                SET x = 1;
                WHILE(x <= @countCustomField)
        		DO
        			SET @qr = CONCAT("SELECT CONCAT(\'IFNULL(CONCAT(\', id, \'), \\"\") AS \', field_name), TEMP.field_name INTO @cutomField, @fieldName FROM (SELECT GROUP_CONCAT(CONCAT(\'custom_field_\', `id`)) AS id , field_name, @rownum := @rownum + 1 AS rank FROM custom_fields, (SELECT @rownum := 0) r WHERE list_id IN (", @listIDs ,") GROUP BY field_name) AS TEMP WHERE rank = ", x , ";");
                    PREPARE qr FROM @qr;
        			EXECUTE qr;
        			DEALLOCATE PREPARE qr;

                    IF LENGTH(@cutomField) > 0 THEN
        				SET custom_field = CONCAT(custom_field, ", ", @cutomField);
                    END IF;

                    SET header_export = CONCAT(header_export, ",\'", @fieldName,"\' AS ", @fieldName);
                    SET string_custom_field = CONCAT(string_custom_field, ", ", @fieldName);

                    SET x = x + 1;
                END WHILE;

                SET @qr = CONCAT("SELECT name, DATE_FORMAT(send_time, \'%M %d %Y %h:%i %p\') AS send_time, sender INTO @nameCampaign, @sentTime, @sender FROM campaign_u_", userID, " WHERE id = ", campaignID, ";");
        	    PREPARE qr FROM @qr;
        	    EXECUTE qr;
        	    DEALLOCATE PREPARE qr;

                SET header_export = CONCAT(header_export, ",\'TimeStamp\' AS report_updated_at");
                SET @string_query = CONCAT("SELECT CAST(queue.phone AS CHAR) AS phone, IFNULL(DATE_FORMAT(CONVERT_TZ(queue.report_updated_at, \' UTC\',\'",timeZone,"\'), \'%M %d %Y %h:%i %p\'), \'\') AS report_updated_at, ", first_name, ", ", last_name, custom_field ," FROM queue_u_", userID, "_c_", campaignID, " AS queue ", left_join);
                SET @filePending = CONCAT(pathFile, "Campaign_", REPLACE(REPLACE(REPLACE(REPLACE(@nameCampaign, "", "_"), "/", "_"), "\'", ""), " ", "_"), "_Pending_Report.csv");
                SET @fileDelivered = CONCAT(pathFile, "Campaign_", REPLACE(REPLACE(REPLACE(REPLACE(@nameCampaign, "", "_"), "/", "_"), "\'", ""), " ", "_"), "_Delivered_Report.csv");
                SET @fileExpired = CONCAT(pathFile, "Campaign_", REPLACE(REPLACE(REPLACE(REPLACE(@nameCampaign, "", "_"), "/", "_"), "\'", ""), " ", "_"), "_Expired_Report.csv");
                SET @fileFailed = CONCAT(pathFile, "Campaign_", REPLACE(REPLACE(REPLACE(REPLACE(@nameCampaign, "", "_"), "/", "_"), "\'", ""), " ", "_"), "_Failed_Report.csv");
                SET @fileDetailed = CONCAT(pathFile, "Campaign_", REPLACE(REPLACE(REPLACE(REPLACE(@nameCampaign, "", "_"), "/", "_"), "\'", ""), " ", "_"), "_Detailed_Report.csv");

                IF encrypted = 0 THEN
					SET @phone = CONCAT("phone AS phone");
                ELSE
					SET @phone = CONCAT("ENCRYPT_PHONE(phone) AS phone");
                END IF;

        		IF pending = 1 THEN
        			-- Export campaign pedding
        			SET @qr = CONCAT("SELECT ", header_export ," UNION ALL SELECT ", @phone ,", first_name, last_name ", string_custom_field ,", report_updated_at INTO OUTFILE \'", @filePending ,"\' FIELDS TERMINATED BY \',\' OPTIONALLY ENCLOSED BY \'\' LINES TERMINATED BY \'\n\' FROM (", @string_query , " WHERE queue.return_status = \'PENDING\') AS TEMP;");
        			PREPARE qr FROM @qr;
        			EXECUTE qr;
        			DEALLOCATE PREPARE qr;
                END IF;

                IF delivered = 1 THEN
        			-- Export campaign delivered
        			SET @qr = CONCAT("SELECT ", header_export ," UNION ALL SELECT ", @phone ,", first_name, last_name ", string_custom_field ,", report_updated_at  INTO OUTFILE \'", @fileDelivered ,"\' FIELDS TERMINATED BY \',\' OPTIONALLY ENCLOSED BY \'\' LINES TERMINATED BY \'\n\' FROM (",@string_query, " WHERE queue.return_status = \'DELIVERED\') AS TEMP;");
        			PREPARE qr FROM @qr;
        			EXECUTE qr;
        			DEALLOCATE PREPARE qr;
                END IF;

                IF failed = 1 THEN
        			-- Export campaign failed
        			SET @qr = CONCAT("SELECT ", header_export ," UNION ALL SELECT ", @phone ,", first_name, last_name ", string_custom_field ,", report_updated_at INTO OUTFILE \'", @fileFailed ,"\' FIELDS TERMINATED BY \',\' OPTIONALLY ENCLOSED BY \'\' LINES TERMINATED BY \'\n\' FROM (",@string_query, " WHERE queue.return_status = \'FAILED\') AS TEMP;");
        			PREPARE qr FROM @qr;
        			EXECUTE qr;
        			DEALLOCATE PREPARE qr;
                END IF;

                IF expired = 1 THEN
        			-- Export campaign expired
        			SET @qr = CONCAT("SELECT ", header_export ," UNION ALL SELECT ", @phone ,", first_name, last_name ", string_custom_field ,", report_updated_at INTO OUTFILE \'", @fileExpired ,"\' FIELDS TERMINATED BY \',\' OPTIONALLY ENCLOSED BY \'\' LINES TERMINATED BY \'\n\' FROM (",@string_query, " WHERE queue.return_status = \'EXPIRED\') AS TEMP;");
        			PREPARE qr FROM @qr;
        			EXECUTE qr;
        			DEALLOCATE PREPARE qr;
                END IF;

        		IF detailed = 1 THEN
        			-- Export detail campaign
                    IF typeUser != \'GROUP1\' THEN
                        SET header_export_detail = CONCAT("\'External message id\' AS return_message_id,\'Destination number\' AS phone,\'Sender\' AS sender,\'Destination country\' AS country,\'Destination network\' AS network,\'Price per message\' AS sum_price_client,\'Currency\' AS currency,\'SMS count\' AS message_count,\'Sent time\' AS sent_time,\'Status\' AS return_status,\'Status Message\' AS return_status_message,\'Message text\' AS message");
                        SET @qr = CONCAT("SELECT ", header_export_detail ," UNION ALL SELECT IFNULL(return_message_id, \'\') AS return_message_id, ", @phone ,", \'", @sender, "\' AS sender, COALESCE(T2.name, T1.country, \'\') AS country, IFNULL(network, \'\') AS network, sum_price_client, \'", defaultCurrency, "\' AS currency, message_count, \'", @sentTime ,"\' AS sent_time, IFNULL(return_status, \'\') AS return_status, IFNULL(IF(return_status = \'FAILED\', return_status_message, null), \'\') AS return_status_message, IFNULL(REPLACE(REPLACE(REPLACE(message, \'\n\', \' \'), \'\r\', \' \'), \'\r\n\', \' \'), \'\') AS message INTO OUTFILE \'", @fileDetailed ,"\' FIELDS TERMINATED BY \',\' OPTIONALLY ENCLOSED BY \'\\"\\\' ESCAPED BY \'\\"\\\' LINES TERMINATED BY \'\n\' FROM queue_u_", userID ,"_c_", campaignID ," AS T1 LEFT JOIN countries AS T2 ON  UPPER(T1.country) = UPPER(T2.code) ;");
                    ELSE
                        SET @qr = CONCAT("SELECT ", header_export_detail ," UNION ALL SELECT IFNULL(return_message_id, \'\') AS return_message_id, ", @phone ,", \'", @sender, "\' AS sender, COALESCE(T2.name, T1.country, \'\') AS country, IFNULL(network, \'\') AS network, sum_price_client, \'", defaultCurrency, "\' AS currency, IF(ported = 0, \'NO\', \'YES\') as ported, message_count, \'", @sentTime ,"\' AS sent_time, IFNULL(DATE_FORMAT(CONVERT_TZ(report_updated_at, \'UTC\', \'", timeZone ,"\'), \'%M %d %Y %h:%i %p\'), \'\') AS report_updated_at, IFNULL(return_status, \'\') AS return_status, IFNULL(IF(return_status = \'FAILED\', return_status_message, null), \'\') AS return_status_message, IFNULL(REPLACE(REPLACE(REPLACE(message, \'\n\', \' \'), \'\r\', \' \'), \'\r\n\', \' \'), \'\') AS message INTO OUTFILE \'", @fileDetailed ,"\' FIELDS TERMINATED BY \',\' OPTIONALLY ENCLOSED BY \'\\"\\\' ESCAPED BY \'\\"\\\' LINES TERMINATED BY \'\n\' FROM queue_u_", userID ,"_c_", campaignID ," AS T1 LEFT JOIN countries AS T2 ON  UPPER(T1.country) = UPPER(T2.code) ;");
                    END IF;

        			PREPARE qr FROM @qr;
        			EXECUTE qr;
        			DEALLOCATE PREPARE qr;
                END IF;


                SELECT @filePending AS filePending, @fileDelivered AS fileDelivered, @fileExpired AS fileExpired, @fileFailed AS fileFailed, @fileDetailed AS fileDetailed;
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
        $sql = "DROP PROCEDURE IF EXISTS export_campaign";
        DB::connection ()->getPdo ()->exec ( $sql );
    }
}
