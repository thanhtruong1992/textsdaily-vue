<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTotalSendCampaignsOfUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // clone campaign item
        DB::unprepared ('CREATE PROCEDURE `total_campagin_of_users`(
        	IN userID VARCHAR(255), -- list id of user. Example: 1,2,3
            IN startDate DATETIME,
            IN endDate DATETIME,
            IN filter VARCHAR(50),
            IN timezone VARCHAR(50),
            IN userType VARCHAR(50),
            IN userCurrency VARCHAR(10)
        )
BEGIN
        	declare x INT default 0;
        	declare user_id VARCHAR(255) default "";
        	declare string_query LONGTEXT default "";

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
        		SET user_id = userID;
        	END IF;

            IF userType != \'GROUP1\' THEN
				SET @currency = CONCAT("\'", userCurrency, "\' AS currency ");
            END IF;

            IF userType = \'GROUP1\' OR userType = \'GROUP2\' THEN
				SET @price = CONCAT(" SUM(sum_price_agency) AS sum_price, sum_price_agency AS price ");
            ELSE
				SET @price = CONCAT(" SUM(sum_price_client) AS sum_price, sum_price_client AS price ");
            END IF;

        	START TRANSACTION;
                IF LENGTH(user_id) > 0 THEN
            		SET @count = (LENGTH(user_id) - LENGTH(REPLACE(user_id, ",", "")))/LENGTH(",") + 1;

            		loop_insert: LOOP
            			SET x = x + 1;
            			SET @user_id = TRIM(REPLACE(SUBSTRING(SUBSTRING_INDEX(user_id, ",", x), LENGTH(SUBSTRING_INDEX(user_id, ",", x-1)) + 1), ",", ""));

                        IF userType = \'GROUP1\' THEN
    						SET @currency = CONCAT(" (SELECT currency FROM users WHERE id = ", @user_id ,") AS currency ");
                        END IF;

            			SET @qr = CONCAT("SELECT GROUP_CONCAT(id SEPARATOR \',\') INTO @listCampaign FROM campaign_u_", @user_id ," WHERE status = \'SENT\' AND CONVERT_TZ(send_time, send_timezone, \'", timezone ,"\') >= \'", startDate ,"\';");
            			PREPARE qr FROM @qr;
            			EXECUTE qr;
            			DEALLOCATE PREPARE qr;

                        IF LENGTH(@listCampaign) > 0 THEN
    						SET @listCampaign = CONCAT(@listCampaign, ",");

                            WHILE (LOCATE(\',\', @listCampaign) > 0) DO
    							SET @campaign_id = SUBSTRING(@listCampaign,1, LOCATE(\',\',@listCampaign)-1);
    							SET @listCampaign = SUBSTRING(@listCampaign, LOCATE(\',\',@listCampaign) + 1);
    							SET @tbl_queue = CONCAT("queue_u_", @user_id, "_c_", @campaign_id);

    							IF LENGTH(string_query) > 0 THEN
    								SET string_query = CONCAT(string_query, " UNION ALL ");
    							END IF;

    							IF filter = \'month\' THEN
                                    SET string_query = CONCAT(string_query, "SELECT DATE_FORMAT(CONVERT_TZ(updated_at, \'UTC\', \'", timezone ,"\'), \'%m\') AS updated_at, sum(message_count) AS message_count, ", @price ,", ", @currency ,", \'", @tbl_queue ,"\' AS table_name FROM ", @tbl_queue ," WHERE status != \'PENDING\' AND CONVERT_TZ(updated_at, \'UTC\', \'", timezone ,"\') BETWEEN \'", startDate ,"\' AND \'", endDate ,"\' group by price, currency, DATE_FORMAT(updated_at, \'%m\')");
                                ELSEIF filter = \'day\' THEN
    								SET string_query = CONCAT(string_query, "SELECT DATE_FORMAT(CONVERT_TZ(updated_at, \'UTC\', \'", timezone ,"\'), \'%d\') AS updated_at, sum(message_count) AS message_count, ", @price ,", ", @currency ,", \'", @tbl_queue ,"\' AS table_name FROM ", @tbl_queue ," WHERE status != \'PENDING\' AND CONVERT_TZ(updated_at, \'UTC\', \'", timezone ,"\') BETWEEN \'", startDate ,"\' AND \'", endDate ,"\' group by price, currency, DATE_FORMAT(updated_at, \'%d\')");
                                ELSE
    								SET string_query = CONCAT(string_query, "SELECT DATE_FORMAT(CONVERT_TZ(updated_at, \'UTC\', \'", timezone ,"\'), \'%H\') AS updated_at, sum(message_count) AS message_count, ", @price ,", ", @currency ,", \'", @tbl_queue ,"\' AS table_name FROM ", @tbl_queue ," WHERE status != \'PENDING\' AND CONVERT_TZ(updated_at, \'UTC\', \'", timezone ,"\') BETWEEN \'", startDate ,"\' AND \'", endDate ,"\' group by price, currency, DATE_FORMAT(updated_at, \'%H\')");
                                END IF;

    						END WHILE;
                        END IF;

            			IF( x = @count) THEN
            				LEAVE loop_insert;
            			END IF;
            		END LOOP loop_insert;

    				IF LENGTH(string_query) > 0 THEN
    					-- STEP TOTAL PRICE ALL CAMPAIGN WITH USER
    					SET @qr = CONCAT("SELECT updated_at AS keyValue, SUM(message_count) AS total_message, SUM(sum_price) AS total_price, price AS price, currency AS currency FROM (", string_query ,") AS TMP GROUP BY TMP.price, TMP.currency, TMP.updated_at;");
    					PREPARE qr FROM @qr;
    					EXECUTE qr;
    					DEALLOCATE PREPARE qr;
    				ELSE
    					SELECT null AS total_message;
    				END IF;
                ELSE
                    SELECT null AS total_message;
                END IF;
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
        $sql2 = "DROP PROCEDURE IF EXISTS total_campagin_of_users";
        DB::connection ()->getPdo ()->exec ( $sql2 );
    }
}
