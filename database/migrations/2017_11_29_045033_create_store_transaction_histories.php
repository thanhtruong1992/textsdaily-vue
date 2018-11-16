<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreTransactionHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::unprepared("CREATE PROCEDURE `transaction_history_campaign`(
        	IN campaingID INT,
            IN userID INT,
            IN typeUser VARCHAR(255),
            IN defaultCurrency VARCHAR(10),
            IN flagCSV BOOLEAN,
            IN headerCSV LONGTEXT,
            IN pathFile LONGTEXT
        )
        BEGIN
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
        		SET @tbl_queue = CONCAT(\"report_list_summary_u_\", userID);

                IF typeUser = \"GROUP1\" THEN
        			SET @price = CONCAT(\" (agency_expenses / totals) AS unit_price, agency_expenses AS total_charge, COALESCE(currency, 'Various Currencies') AS currency\");
        		ELSE
        			SET @price = CONCAT(\" (client_expenses / totals) AS unit_price, client_expenses AS total_charge, '\", defaultCurrency, \"' AS currency \");
                END IF;

                IF flagCSV = 1 THEN
        			SET @qr = CONCAT(\"SELECT \", headerCSV ,\" UNION SELECT totals, service_provider, COALESCE(T2.name, T1.country, 'Unknown') as country, IFNULL(network, 'Unknown') AS network, \", @price , \" INTO OUTFILE '\", pathFile ,\"' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '' LINES TERMINATED BY '\n' FROM \", @tbl_queue, \" AS T1 LEFT JOIN countries AS T2 ON T1.country = T2.code WHERE campaign_id = \", campaingID ,\" GROUP BY country, network, service_provider, currency ORDER BY country, network, service_provider\");
                ELSE
                    SET @qr = CONCAT(\"SELECT totals, service_provider, COALESCE(T2.name, T1.country, 'Unknown') as country, IFNULL(network, 'Unknown') as network, \", @price , \" FROM \", @tbl_queue, \" AS T1 LEFT JOIN countries AS T2 ON T1.country = T2.code WHERE campaign_id = \", campaingID ,\" GROUP BY country, network, service_provider, currency ORDER BY country, network, service_provider;\");
                END IF;

        		PREPARE qr FROM @qr;
        		EXECUTE qr;
        		DEALLOCATE PREPARE qr;
            COMMIT;
        END");

        DB::unprepared("CREATE PROCEDURE `transaction_history_campaigns`(
        	IN listUser LONGTEXT,
            IN dateFrom VARCHAR(50),
            IN dateTo VARCHAR(50),
            IN timezone VARCHAR(100),
            IN typeUser VARCHAR(10),
            IN defaultCurrency VARCHAR(10),
            IN flagCSV BOOLEAN,
            IN headerCSV LONGTEXT,
            IN pathFile LONGTEXT,
            IN numberPage INT
        )
        BEGIN
            DECLARE str_query LONGTEXT DEFAULT \"\";
            DECLARE query_user LONGTEXT DEFAULT \"\";
            DECLARE all_query MEDIUMTEXT DEFAULT \"\";
            DECLARE totalDataOfPage INT DEFAULT 10;
            
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
                SET @x = 1;
        		WHILE (LOCATE(',', listUser) > 0) DO
        			SET @user_id = SUBSTRING(listUser,1, LOCATE(',',listUser)-1);
        			SET listUser = SUBSTRING(listUser, LOCATE(',',listUser) + 1);
        			SET @tbl_campaign = CONCAT(\"campaign_u_\", @user_id);
        			SET @tbl_report = CONCAT(\"report_list_summary_u_\", @user_id);
                    SET str_query = CONCAT(\" HAVING status = 'sent'\");

                    IF typeUser = \"GROUP1\" THEN
        				SET @price = CONCAT(\" COALESCE(T1.currency, 'Various Currencies') AS currency, T1.agency_expenses AS total_expenses \");
        				SET query_user = CONCAT(\"SELECT \", @user_id ,\" AS user_id, T1.name, T1.billing_type FROM users AS T1 INNER JOIN users T2 ON T1.id = T2.parent_id AND T2.id = \", @user_id);
        			ELSE
        				SET @price = CONCAT(\"'\", defaultCurrency, \"' AS currency, T1.client_expenses AS total_expenses \");
                        SET query_user = CONCAT(\"SELECT id AS user_id, name, billing_type FROM users WHERE id = \", @user_id);
        			END IF;

                    IF LENGTH(dateFrom) > 0 && LENGTH(dateTo) > 0 THEN
        				SET str_query = CONCAT(str_query, \" AND (CONVERT_TZ(send_time, send_timezone, 'UTC') BETWEEN '\", dateFrom ,\"' AND '\", dateTo ,\"')\");
                    ELSEIF LENGTH(dateFrom) > 0 THEN
        				SET str_query = CONCAT(str_query, \" AND CONVERT_TZ(send_time, send_timezone, 'UTC') >= '\", dateFrom ,\"'\");
                    ELSEIF LENGTH(dateTo) > 0 THEN
        				SET str_query = CONCAT(str_query, \" AND CONVERT_TZ(send_time, send_timezone, 'UTC') <= '\", dateTo ,\"'\");
                    END IF;

        			IF LENGTH(all_query) = 0 THEN
        				SET all_query = CONCAT(\"SELECT *, CAST(IF(total_expenses != 0, SUM(total_expenses), 'Unknown') AS CHAR) AS total FROM ( SELECT '\", @tbl_campaign ,\"' AS tbl_campaign, T3.user_id, T1.campaign_id, T2.name AS campaign_name, CONVERT_TZ(T2.send_time, T2.send_timezone, '\",timezone,\"') AS date_send, \", @price ,\", T3.name AS client_name, T3.billing_type AS client_type, T2.send_time, T2.status, T2.send_timezone FROM \", @tbl_report ,\" AS T1 LEFT JOIN \", @tbl_campaign ,\" AS T2 ON T1.campaign_id = T2.id LEFT JOIN (\", query_user ,\") AS T3 ON T2.user_id = T3.user_id \", str_query, \") AS TEMP\",@x,\" GROUP BY campaign_id, currency \");
                    ELSE
        				SET all_query = CONCAT(all_query, \" UNION SELECT *, CAST(IF(total_expenses != 0, SUM(total_expenses), 'Unknown') AS CHAR) AS total FROM ( SELECT '\", @tbl_campaign ,\"' AS tbl_campaign, T3.user_id, T1.campaign_id, T2.name AS campaign_name, CONVERT_TZ(T2.send_time, T2.send_timezone, '\",timezone,\"') AS date_send, \", @price ,\", T3.name AS client_name, T3.billing_type AS client_type, T2.send_time, T2.status, T2.send_timezone FROM \", @tbl_report ,\" AS T1 LEFT JOIN \", @tbl_campaign ,\" AS T2 ON T1.campaign_id = T2.id LEFT JOIN (\", query_user ,\") AS T3 ON T2.user_id = T3.user_id \", str_query, \") AS TEMP\",@x,\" GROUP BY campaign_id, currency \");
                    END IF;

                    SET @x = @x + 1;
        		END WHILE;

                IF flagCSV = 1 THEN
        			SET @qr = CONCAT(\"SELECT \", headerCSV , \" UNION SELECT DATE_FORMAT(date_send, '%d-%M-%Y %H:%i') as date_send, campaign_name, client_name, client_type, total, currency INTO OUTFILE '\", pathFile ,\"' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\\\"\' LINES TERMINATED BY '\n' FROM (\", all_query, \") AS T1;\");
                ELSE
        			SET @qr = CONCAT(\"SELECT COUNT(*) INTO @totalData FROM (\", all_query ,\") AS T1;\");
                    PREPARE qr FROM @qr;
        			EXECUTE qr;
        			DEALLOCATE PREPARE qr;

        			SET @qr = CONCAT(\"SELECT *, \",@totalData ,\" AS totalData FROM (\", all_query ,\") AS T1 ORDER BY T1.date_send DESC LIMIT \", totalDataOfPage ,\" OFFSET \", totalDataOfPage * numberPage ,\";\");
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
        $sql = "DROP PROCEDURE IF EXISTS transaction_history_campaign";
        DB::connection ()->getPdo ()->exec ( $sql );

        $sql = "DROP PROCEDURE IF EXISTS transaction_history_campaigns";
        DB::connection ()->getPdo ()->exec ( $sql );
    }
}
