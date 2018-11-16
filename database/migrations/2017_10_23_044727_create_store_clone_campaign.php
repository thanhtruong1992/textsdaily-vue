<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreCloneCampaign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // clone campaign item
        DB::unprepared ("CREATE PROCEDURE `clone_data_campaign`(
                	IN campaign_id INT,
                    IN user_id INT
                )
        BEGIN
            DECLARE table_campaign	VARCHAR(255) DEFAULT CONCAT('campaign_u_', user_id);
            DECLARE table_campaign_recipient VARCHAR(255) DEFAULT CONCAT('campaign_recipients_u_', user_id);
            DECLARE table_campaign_link VARCHAR(255) DEFAULT CONCAT('campaign_links_u_', user_id);
            DECLARE campaign_new_id INT DEFAULT 99;
            
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
            -- STEP INSERT CAMPAIGN
            SET @qr = CONCAT('INSERT INTO ', table_campaign, '(`user_id`, `name`, `sender`, `language`, `message`, `valid_period`, `created_by`, `updated_by`, `created_at`, `updated_at`) SELECT `user_id`, `name`, `sender`, `language`, `message`, `valid_period`, `created_by`, `updated_by`, \"', NOW() ,'\", \"', NOW() ,'\" FROM ',  table_campaign, ' WHERE `id`=', campaign_id, ';');
            PREPARE qr FROM @qr;
            EXECUTE qr;
            DEALLOCATE PREPARE qr;

            -- STEP GET LASTED ID OF CAMPAIGN
            SET @CampaignID = LAST_INSERT_ID();

            IF @CampaignID > 0 THEN
                SET campaign_new_id = @CampaignID;

                -- STEP INSERT CAMPAIGN RECIPIENTS
                SET @qr = CONCAT('INSERT INTO ', table_campaign_recipient, '(user_id, campaign_id, list_id, created_by, updated_by, created_at, updated_at) SELECT user_id, ', campaign_new_id ,', list_id, created_by, updated_by, \"', NOW() ,'\", \"', NOW() ,'\" FROM ',  table_campaign_recipient, ' WHERE `campaign_id`=', campaign_id, ';');
                PREPARE qr FROM @qr;
                EXECUTE qr;
                DEALLOCATE PREPARE qr;

                -- STEP INSERT CAMPAIGN LINK
                -- SET @qr = CONCAT('INSERT INTO ', table_campaign_link, '(campaign_id, url, short_link, created_by, updated_by, created_at, updated_at) SELECT ', campaign_new_id ,', url, short_link, created_by, updated_by, \"', NOW() ,'\", \"', NOW() ,'\" FROM ',  table_campaign_link, ' WHERE `campaign_id`=', campaign_id, ';');
                -- PREPARE qr FROM @qr;
                -- EXECUTE qr;
                -- DEALLOCATE PREPARE qr;
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
        $sql2 = "DROP PROCEDURE IF EXISTS clone_data_campaign";
        DB::connection ()->getPdo ()->exec ( $sql2 );
    }
}
