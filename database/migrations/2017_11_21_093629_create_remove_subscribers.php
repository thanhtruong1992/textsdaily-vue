<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRemoveSubscribers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::unprepared("CREATE PROCEDURE `remove_subscribers`(
            IN listID INT,
            IN supperssedID INT,
            IN flagStatus VARCHAR(255),
            IN flagSupperssed BOOLEAN
        )
        BEGIN
        	DECLARE string_add LONGTEXT DEFAULT \"\";
            DECLARE string_delete LONGTEXT DEFAULT \"\";
            DECLARE tbl_subscriber LONGTEXT DEFAULT \"\";
            DECLARE tbl_supperssion LONGTEXT DEFAULT \"\";
            
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
            
            SET tbl_subscriber = CONCAT(\"subscribers_l_\", listID);
            SET tbl_supperssion = CONCAT(\"subscribers_l_\", supperssedID);

            START TRANSACTION;
        		IF flagStatus = 'SUBSCRIBED' OR flagStatus = 'UNSUBSCRIBED' THEN
        			-- STEP DATA REMOVE
                    SET @qr = CONCAT(\"SELECT GROUP_CONCAT(phone SEPARATOR '\n') INTO @DataRemove FROM \", tbl_subscriber ,\" WHERE status = '\", flagStatus ,\"';\");
                    PREPARE qr FROM @qr;
        			EXECUTE qr;
        			DEALLOCATE PREPARE qr;

        			SET string_add = CONCAT(\"(SELECT phone, updated_by, created_by, '\", now() ,\"', '\", now() ,\"' FROM \", tbl_subscriber ,\" WHERE status = '\", flagStatus ,\"')\");
                    SET string_delete = CONCAT(\"DELETE FROM \", tbl_subscriber ,\" WHERE status = '\", flagStatus ,\"';\");
                ELSEIF flagStatus = 'SUPPERSSED' THEN
        			-- STEP DATA REMOVE
                    SET @qr = CONCAT(\"SELECT GROUP_CONCAT(phone SEPARATOR '\n') INTO @DataRemove FROM(SELECT T1.phone FROM \", tbl_subscriber ,\" AS T1 INNER JOIN \", tbl_supperssion ,\" AS T2 ON T1.phone = T2.phone GROUP BY T1.phone) AS T3;\");
                    PREPARE qr FROM @qr;
        			EXECUTE qr;
        			DEALLOCATE PREPARE qr;

                    SET string_delete = CONCAT(\"DELETE T1 FROM \", tbl_subscriber ,\" AS T1 INNER JOIN \", tbl_supperssion ,\" AS T2 ON T1.phone = T2.phone;\");
                ELSE
        			SET @tbl_temp = CONCAT(\"subscribers_remove_l_\", listID);

                    -- STEP TOTAL SUBSCRIBERS
                    SET @qr = CONCAT(\"SELECT COUNT(1) INTO @TotalSubscribers FROM \", @tbl_temp ,\";\");
                    PREPARE qr FROM @qr;
        			EXECUTE qr;
        			DEALLOCATE PREPARE qr;

                    -- STEP VALIDATE PHONE SUBCRIBER
        			SET @qr = CONCAT(\"SELECT GROUP_CONCAT(TEMP.phone SEPARATOR '\n') INTO @DataInvalid FROM \", @tbl_temp , \" AS TEMP WHERE TRIM(TEMP.phone) NOT REGEXP '^([\+]?[0-9 ]){1,15}$';\");
        			PREPARE qr FROM @qr;
        			EXECUTE qr;
        			DEALLOCATE PREPARE qr;

        			-- STEP DELETE INVALID PHONE SUBCRIBER
        			SET @qr = CONCAT(\"DELETE FROM \", @tbl_temp, \" WHERE TRIM(phone) NOT REGEXP '^([\+]?[0-9 ]){1,15}$';\");
        			PREPARE qr FROM @qr;
        			EXECUTE qr;
        			DEALLOCATE PREPARE qr;

                    -- STEP DATA REMOVE
        			SET @qr = CONCAT(\"SELECT GROUP_CONCAT(SUBS.phone SEPARATOR '\n') INTO @DataRemove FROM \", tbl_subscriber, \" AS SUBS INNER JOIN \", @tbl_temp, \" AS TEMP ON TRIM(SUBS.phone) = REGEXP_REPLACE(TEMP.`phone`, '[+, ]', '');\");
        			PREPARE qr FROM @qr;
        			EXECUTE qr;
        			DEALLOCATE PREPARE qr;

                    -- STEP DATA SKIP
        			SET @qr = CONCAT(\"SELECT GROUP_CONCAT(TEMP.phone SEPARATOR '\n') INTO @DataSkip FROM \", @tbl_temp, \" AS  TEMP LEFT JOIN \", tbl_subscriber ,\" AS SUBS ON TRIM(SUBS.phone) = REGEXP_REPLACE(TEMP.`phone`, '[+, ]', '') WHERE SUBS.phone IS NULL;\");
        			PREPARE qr FROM @qr;
        			EXECUTE qr;
        			DEALLOCATE PREPARE qr;

        			SET string_add = CONCAT(\"(SELECT T1.phone, T1.updated_by, T1.created_by, '\", now() ,\"', '\", now() ,\"' FROM \", tbl_subscriber ,\" AS T1 INNER JOIN \", @tbl_temp ,\" AS T2 ON TRIM(T1.phone) = REGEXP_REPLACE(T2.`phone`, '[+, ]', ''))\");
                    SET string_delete = CONCAT(\"DELETE T1 FROM \", tbl_subscriber ,\" AS T1 INNER JOIN \", @tbl_temp ,\" AS T2 ON TRIM(T1.phone) = REGEXP_REPLACE(T2.`phone`, '[+, ]', '');\");
                END IF;

                -- STEP ADSD TABLE SUPPERSSED SUBSCRIBERS
                IF flagSupperssed = 1 AND flagStatus != 'SUPPERSSED' THEN
        			SET @qr = CONCAT(\"INSERT INTO \", tbl_supperssion ,\" (phone, updated_by, created_by, created_at, updated_at) \", string_add, \";\");
        			PREPARE qr FROM @qr;
        			EXECUTE qr;
        			DEALLOCATE PREPARE qr;
                END IF;

                -- STEP DELETE SUBSCRIBER
                SET @qr = CONCAT(string_delete);
        		PREPARE qr FROM @qr;
        		EXECUTE qr;
        		DEALLOCATE PREPARE qr;

                -- STEP REMOVE TABLE TEMP SUBSCRIBER
                IF flagStatus = 'MOBILE' THEN
        			SET @qr = CONCAT('DROP TABLE subscribers_remove_l_', listID, ';');
        			PREPARE qr FROM @qr;
        			EXECUTE qr;
        			DEALLOCATE PREPARE qr;
                END IF;

                SELECT @TotalSubscribers AS TotalSubscribers, @DataRemove AS DataRemove, @DataSkip AS DataSkip, @DataInvalid AS DataInvalid;
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
        $sql = "DROP PROCEDURE IF EXISTS remove_subscribers";
        DB::connection ()->getPdo ()->exec ( $sql );
    }
}
