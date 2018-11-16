<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StoreGetCampaignSent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::unprepared("
            CREATE PROCEDURE `getCampaignSent`()
            BEGIN
            	DECLARE bDone INT DEFAULT FALSE;
                DECLARE tblName VARCHAR(255);
                DECLARE queryStr LONGTEXT DEFAULT NULL;
                DECLARE countQueues INT DEFAULT 0;
                DECLARE firstDataUserID INT DEFAULT NULL;
                DECLARE firstDataCampaignID INT DEFAULT NULL;

                DECLARE curs CURSOR FOR SELECT DISTINCT table_name FROM information_schema.tables WHERE table_name LIKE 'campaign_u_%' AND table_name <> 'campaign_u_template';
            	DECLARE CONTINUE HANDLER FOR NOT FOUND SET bDone = TRUE;

                #1. CHECK DATA IN crontab_queues
                SELECT COUNT(1) INTO countQueues FROM crontab_queues WHERE `type`= 'CAMPAIGN_SENT_STATUS';

                #2. COLLECT DATA FOR crontab_queues IF NULL
                IF countQueues = 0 THEN

            		OPEN curs;
            			allTable: LOOP
            				FETCH curs INTO tblName;

            				IF bDone THEN
            					LEAVE allTable;
            				END IF;
                            
                            SET @user_id = substring_index(tblName, '_', -1);
                            
                            SET @qr = CONCAT(\"SELECT GROUP_CONCAT(id SEPARATOR ',') INTO @listCampaign FROM campaign_u_\", @user_id ,\" WHERE status = 'SENT' AND tracking_delivery_report IN ('PENDING', 'PROCESSING');\");
							PREPARE qr FROM @qr;
							EXECUTE qr;
							DEALLOCATE PREPARE qr;
                            
                            -- SET @countQueue = 0;
                            SET @campaignIDS = \"\";
							
							IF LENGTH(@listCampaign) > 0 AND @listCampaign IS NOT NULL THEN
                            
								SET @listCampaign = CONCAT(@listCampaign, \",\");

								WHILE (LOCATE(',', @listCampaign) > 0) DO
									SET @campaign_id = SUBSTRING(@listCampaign,1, LOCATE(',',@listCampaign)-1);
									SET @listCampaign = SUBSTRING(@listCampaign, LOCATE(',',@listCampaign) + 1);
									SET @tbl_queue = CONCAT(\"queue_u_\", @user_id, \"_c_\", @campaign_id);

									SET @query = CONCAT('SELECT count(1) INTO @countQueue FROM ', @tbl_queue, ' WHERE report_status = \"PENDING\" AND return_status = \"PENDING\" AND status = \"SENT\";');
									PREPARE qr FROM @query;
									EXECUTE qr;
									DEALLOCATE PREPARE qr;
                                   
                                    -- check count queue pending
                                    IF @countQueue > 0 AND @countQueue IS NOT NULL THEN
										-- add id campaign into campaignID
										IF LENGTH(@campaignIDS) = 0 THEN
											SET @campaignIDS = @campaign_id;
                                        ELSE
											SET @campaignIDS = CONCAT(@campaignIDS, \",\", @campaign_id);
                                        END IF;
                                    END IF;
									
								END WHILE;
							END IF;
                            
                            IF LENGTH(@campaignIDS) > 0 AND @campaignIDS IS NOT NULL THEN
								IF queryStr IS NULL THEN
									SET queryStr = CONCAT('SELECT \"CAMPAIGN_SENT_STATUS\" AS type, user_id, id AS data_id, tracking_delivery_report_update_at FROM ', tblName, ' WHERE status = \"SENT\" AND tracking_delivery_report IN (\"PENDING\", \"PROCESSING\") AND id IN (', @campaignIDS ,')');
								ELSE
									SET queryStr = CONCAT(queryStr, ' UNION SELECT \"CAMPAIGN_SENT_STATUS\" AS type, user_id, id AS data_id, tracking_delivery_report_update_at FROM ', tblName, ' WHERE status = \"SENT\" AND tracking_delivery_report IN (\"PENDING\", \"PROCESSING\") AND id IN (', @campaignIDS ,')');
								END IF;
                            END IF;

            			END LOOP;
            		CLOSE curs;

                    #2.1. INSERT DATA TO crontab_queues
                    IF LENGTH(queryStr) > 0 THEN
						SET @query = CONCAT('INSERT INTO crontab_queues(`type`, `user_id`, `data_id`, `created_at`) (SELECT `type`, `user_id`, `data_id`, NOW() AS created_at FROM (', queryStr, ') AS tmp ORDER BY tracking_delivery_report_update_at',');');
						PREPARE qr FROM @query;
						EXECUTE qr;
						DEALLOCATE PREPARE qr;
                    END IF;
                    
                END IF;

                #3. GET FIRST ID CAMPAIGN
                SELECT id, user_id, data_id INTO @id, firstDataUserID, firstDataCampaignID FROM crontab_queues WHERE `type`= 'CAMPAIGN_SENT_STATUS' LIMIT 0,1;

                #4. DELETE FIRST ID CAMPAIGN
                DELETE FROM crontab_queues WHERE `id` = @id;

                #5 RETURN DATA
                SELECT firstDataUserID AS user_id, firstDataCampaignID AS campaign_id;
            END
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        DB::unprepared ( "DROP PROCEDURE IF EXISTS getCampaignSent" );
    }
}
