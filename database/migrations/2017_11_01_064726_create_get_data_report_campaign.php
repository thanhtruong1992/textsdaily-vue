<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGetDataReportCampaign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE PROCEDURE `get_data_report_campaign`(
        	IN userID LONGTEXT,
            IN typeUser VARCHAR(255),
            IN search LONGTEXT,
            IN numberPage INT
        )
        BEGIN
        	DECLARE tbl_campaign VARCHAR(255) DEFAULT \"\";
            DECLARE tbl_report VARCHAR(255) DEFAULT \"\";
            DECLARE string_query LONGTEXT DEFAULT \"\";
            DECLARE user_id VARCHAR(255) DEFAULT \"\";
			DECLARE string_user LONGTEXT DEFAULT \"\";
            DECLARE x INT DEFAULT 0;
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

            IF LENGTH(userID) > 0 THEN
        		SET userID = CONCAT(userID, \",\");

        		WHILE (LOCATE(',', userID) > 0) DO
					SET user_id = SUBSTRING(userID,1, LOCATE(',',userID)-1);
					SET userID = SUBSTRING(userID, LOCATE(',',userID) + 1);
					SET tbl_campaign = CONCAT(\"campaign_u_\", user_id);
					SET tbl_report = CONCAT(\"report_list_summary_u_\", user_id);

                    IF typeUser = 'GROUP1' THEN
						SET string_user = CONCAT(\" LEFT JOIN (SELECT U1.id, U2.name FROM users AS U1 INNER JOIN users AS U2 ON U1.parent_id = U2.id AND U1.id = \", user_id, \") AS T3  ON T3.id = T1.user_id\");
					ELSE
						SET string_user = CONCAT(\" LEFT JOIN users AS T3 ON T1.user_id = T3.id\");
					END IF;

        			IF LENGTH(string_query) = 0 THEN
        				SET string_query = CONCAT(\"SELECT * FROM (SELECT T1.id, T1.user_id, T3.name AS name_of_user, T1.name, COALESCE(T2.totals, 0) AS totals, COALESCE(T2.delivered, 0) AS delivered, T1.send_time, T1.send_timezone, T1.created_at, CONCAT(COALESCE(ROUND(T2.delivered / T2.totals * 100, 0), 0), '%') as delivered_rate FROM (SELECT * FROM \", tbl_campaign ,\" WHERE status = 'SENT') AS T1 LEFT JOIN (SELECT campaign_id, SUM(totals) AS totals, SUM(delivered) AS delivered FROM \", tbl_report ,\" GROUP BY campaign_id) AS T2 ON T1.id = T2.campaign_id \", string_user, \") AS T\", x);
        			ELSE
        				SET string_query = CONCAT(string_query, \" UNION ALL SELECT * FROM (SELECT T1.id, T1.user_id, T3.name AS name_of_user, T1.name, COALESCE(T2.totals, 0) AS totals, COALESCE(T2.delivered, 0) AS delivered, T1.send_time, T1.send_timezone, T1.created_at, CONCAT(COALESCE(ROUND(T2.delivered / T2.totals * 100, 0), 0), '%') as delivered_rate FROM (SELECT * FROM \", tbl_campaign ,\" WHERE status = 'SENT') AS T1 LEFT JOIN (SELECT campaign_id, SUM(totals) AS totals, SUM(delivered) AS delivered FROM \", tbl_report ,\" GROUP BY campaign_id) AS T2 ON T1.id = T2.campaign_id \", string_user ,\") AS T\", x);
        			END IF;

        		END WHILE;
        	END IF;

            START TRANSACTION;
        		SET @qr = CONCAT(\"SELECT count(*) INTO @countTotal FROM (\", string_query, \") AS TMP WHERE TMP.name LIKE '%\", search ,\"%';\");
				PREPARE qr FROM @qr;
				EXECUTE qr;
				DEALLOCATE PREPARE qr;

        		SET @qr = CONCAT(\"SELECT *, \", @countTotal ,\" AS total_data FROM (\", string_query, \") AS TMP WHERE TMP.name LIKE '%\", search ,\"%' ORDER BY TMP.created_at DESC LIMIT \", totalDataOfPage ,\" OFFSET \", totalDataOfPage * numberPage ,\";\");
                PREPARE qr FROM @qr;
        		EXECUTE qr;
        		DEALLOCATE PREPARE qr;
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
    	$sql = "DROP PROCEDURE IF EXISTS get_data_report_campaign";
    	DB::connection ()->getPdo ()->exec ( $sql );
    }
}
