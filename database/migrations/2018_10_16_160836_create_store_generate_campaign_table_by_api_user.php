<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreGenerateCampaignTableByApiUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared ( "CREATE PROCEDURE generateCampaignTableByApiUser( IN user_id INT )
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

                    IF(user_id IS NOT NULL) THEN

                        SET @qrDropTable = CONCAT('DROP TABLE IF EXISTS campaign_links_u_', user_id, ', campaign_u_', user_id);
                        PREPARE qr FROM @qrDropTable;
                        EXECUTE qr;
                        DEALLOCATE PREPARE qr;

                        SET @queryCampaign = CONCAT('CREATE TABLE campaign_u_', user_id, ' LIKE campaign_u_template;');
                        PREPARE qr FROM @queryCampaign;
                        EXECUTE qr;
                        DEALLOCATE PREPARE qr;

                        SET @qrFKCampaign = CONCAT('ALTER TABLE campaign_u_', user_id, ' ADD FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;');
                        PREPARE qr FROM @qrFKCampaign;
                        EXECUTE qr;
                        DEALLOCATE PREPARE qr;

                        SET @queryCampaignLink = CONCAT('CREATE TABLE campaign_links_u_', user_id, ' LIKE campaign_links_u_template;');
                        PREPARE qr FROM @queryCampaignLink;
                        EXECUTE qr;
                        DEALLOCATE PREPARE qr;

                        SET @qrFKCampaignLink = CONCAT('ALTER TABLE campaign_links_u_', user_id, ' ADD FOREIGN KEY (campaign_id) REFERENCES campaign_u_', user_id, '(id) ON UPDATE CASCADE ON DELETE CASCADE;');
                        PREPARE qr FROM @qrFKCampaignLink;
                        EXECUTE qr;
                        DEALLOCATE PREPARE qr;

                    END IF;

                COMMIT;
            END
        " );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Stored procedures
        DB::unprepared ( "DROP PROCEDURE IF EXISTS generateCampaignTableByApiUser" );
    }
}
