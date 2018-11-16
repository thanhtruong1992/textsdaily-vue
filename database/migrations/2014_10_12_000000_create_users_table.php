<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191);
            $table->string('email')->unique();
            $table->string('password', 255);
            $table->string('remember_token', 100)->nullable();
            $table->string('avatar', 255)->nullable();
            $table->string('host_name', 255)->nullable();
            $table->integer('agency_id')->unsigned();
            $table->integer('parent_id')->nullable();
            $table->string('country', 3);
            $table->string('language', 5)->default('en');
            $table->string('time_zone', 45);
            $table->enum('status', ['ENABLED', 'DISABLED'])->default('ENABLED');
            $table->enum('type', ['GROUP1', 'GROUP2', 'GROUP3', 'GROUP4'])->default('GROUP3');
            $table->integer('reader_id')->unsigned()->nullable();
            $table->boolean('encrypted')->default(0);
            $table->boolean('blocked')->default(0);
            $table->enum('billing_type',['ONE_TIME', 'MONTHLY', 'UNLIMITED'])->default('ONE_TIME');
            $table->double('credits')->default(0);
            $table->double('credits_usage')->default(0);
            $table->double('credits_limit')->default(0);
            $table->string('currency', 3)->default('USD');
            $table->double('default_price_sms')->default(0);
            $table->boolean('is_tracking_link')->default(true);
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
        });

        // Stored procedures
        DB::unprepared ( "CREATE PROCEDURE generateCampaignTableByUser( IN user_id INT )
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

                        SET @qrDropTable = CONCAT('DROP TABLE IF EXISTS campaign_stats_link_u_', user_id, ', campaign_links_u_', user_id, ', campaign_recipients_u_', user_id, ', campaign_u_', user_id, ', inbound_message_', user_id);
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

                        SET @queryCampaignRecipient = CONCAT('CREATE TABLE campaign_recipients_u_', user_id, ' LIKE campaign_recipients_u_template;');
                        PREPARE qr FROM @queryCampaignRecipient;
                        EXECUTE qr;
                        DEALLOCATE PREPARE qr;

                        SET @qrFKCampaignRecipient = CONCAT('ALTER TABLE campaign_recipients_u_', user_id, ' ADD FOREIGN KEY (campaign_id) REFERENCES campaign_u_', user_id, '(id) ON UPDATE CASCADE ON DELETE CASCADE;');
                        PREPARE qr FROM @qrFKCampaignRecipient;
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

                        SET @queryCampaignStatsLink = CONCAT('CREATE TABLE campaign_stats_link_u_', user_id, ' LIKE campaign_stats_link_u_template;');
                        PREPARE qr FROM @queryCampaignStatsLink;
                        EXECUTE qr;
                        DEALLOCATE PREPARE qr;

                        SET @qrFKCampaignStatsLink = CONCAT('ALTER TABLE campaign_stats_link_u_', user_id, ' ADD FOREIGN KEY (link_id) REFERENCES campaign_links_u_', user_id, '(id) ON UPDATE CASCADE ON DELETE CASCADE;');
                        PREPARE qr FROM @qrFKCampaignStatsLink;
                        EXECUTE qr;
                        DEALLOCATE PREPARE qr;

                        SET @queryInboundMessage = CONCAT('CREATE TABLE inbound_messages_u_', user_id, ' LIKE inbound_messages_u_template;');
                        PREPARE qr FROM @queryInboundMessage;
                        EXECUTE qr;
                        DEALLOCATE PREPARE qr;

                        SET @qrFKInboundMessage = CONCAT('ALTER TABLE inbound_messages_u_', user_id, ' ADD FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL;');
                        PREPARE qr FROM @qrFKInboundMessage;
                        EXECUTE qr;
                        DEALLOCATE PREPARE qr;

                    END IF;

                COMMIT;
            END
        " );

        // Stored procedures remove all Campaign table user
        DB::unprepared ( "CREATE PROCEDURE removeCampaignTableByUser( IN user_id INT )
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

                        SET @qrDropTable = CONCAT('DROP TABLE IF EXISTS campaign_stats_link_u_', user_id, ', campaign_links_u_', user_id, ', campaign_recipients_u_', user_id, ', campaign_u_', user_id);
                        PREPARE qr FROM @qrDropTable;
                        EXECUTE qr;
                        DEALLOCATE PREPARE qr;
                    END IF;

                COMMIT;
            END
        " );

        // Stored procedures remove queue and price table by user
        DB::unprepared ( "CREATE PROCEDURE removeQueueTableByUser( IN user_id INT, IN campaign_id INT )
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

                    IF(user_id IS NOT NULL AND campaign_id IS NOT NULL) THEN

                        SET @qrDropQueueTable = CONCAT('DROP TABLE IF EXISTS queue_u_', user_id, '_c_', campaign_id, ';');
                        PREPARE qr FROM @qrDropQueueTable;
                        EXECUTE qr;
                        DEALLOCATE PREPARE qr;

                        SET @qrDropTable = CONCAT('DROP TABLE IF EXISTS price_configuration_u_', user_id);
                        PREPARE qr FROM @qrDropTable;
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
        Schema::dropIfExists('users');

        // Stored procedures
        DB::unprepared ( "DROP PROCEDURE IF EXISTS generateCampaignTableByUser" );
        DB::unprepared ( "DROP PROCEDURE IF EXISTS removeCampaignTableByUser" );
    }
}
