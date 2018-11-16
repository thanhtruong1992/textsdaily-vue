<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateCampaignTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        //
        Schema::create ( 'campaign_u_template', function (Blueprint $table) {
            $table->increments ( 'id' );
            $table->unsignedInteger ( 'user_id' );
            $table->string ( 'name' );
            $table->enum ( 'status', [
                    'DRAFT',
                    'READY',
                    'SENDING',
                    'PAUSED',
                    'SENT',
                    'FAILED'
            ] )->default ( 'DRAFT' );
            $table->boolean( 'is_api' )->default ( 0 );
            $table->string ( 'sender', 191 );
            $table->enum ( 'language', [
                    'ASCII',
                    'UNICODE'
            ] )->default ( 'ASCII' );
            $table->text ( 'message' );
            $table->integer ( 'valid_period' )->nullable ();
            $table->double ( 'estimated_cost' )->default ( 0 );
            $table->enum ( 'schedule_type', [
                    'NOT_SCHEDULED',
                    'IMMEDIATE',
                    'FUTURE'
            ] )->default ( 'NOT_SCHEDULED' );
            $table->dateTime ( 'send_time' )->nullable ();
            $table->string ( 'send_timezone', 45 )->nullable ();
            $table->dateTime ( 'send_process_started_on' )->nullable ();
            $table->dateTime ( 'send_process_finished_on' )->nullable ();
            $table->unsignedInteger ( 'total_recipients' )->default ( 0 );
            $table->unsignedInteger ( 'total_sent' )->default ( 0 );
            $table->unsignedInteger ( 'total_failed' )->default ( 0 );
            $table->unsignedInteger ( 'total_clicks' )->default ( 0 );
            $table->unsignedInteger ( 'unique_clicks' )->default ( 0 );
            $table->double ( 'benchmark_per_second' )->default ( 0 );
            $table->text ( 'notification_emails' )->nullable ();
            $table->enum('tracking_delivery_report', ['PENDING', 'PROCESSING', 'PROCESSED'])->default ( 'PENDING' );
            $table->timestamp('tracking_delivery_report_update_at')->nullable ();
            $table->enum('backend_statistic_report', ['PENDING', 'PROCESSING', 'PROCESSED'])->default ( 'PENDING' );
            $table->timestamp('backend_statistic_report_updated_at')->nullable ();
            $table->unsignedInteger ( 'created_by' )->nullable ();
            $table->unsignedInteger ( 'updated_by' )->nullable ();
            $table->timestamps ();
            // Index
            $table->index ( 'name' );
            $table->index ( 'status' );
            $table->index ( 'schedule_type' );
            $table->index ( 'send_time' );
            $table->index('tracking_delivery_report');
            $table->index('tracking_delivery_report_update_at');
            $table->index ( 'backend_statistic_report' );
            $table->index('backend_statistic_report_updated_at');
            $table->index ( 'language' );
        } );

        // delete campaign item
        DB::unprepared ( "CREATE PROCEDURE `delete_campaign_id`(
            	IN user_id INT,
            	IN campaign_id INT
            )
            BEGIN

                DECLARE int_user_index INT DEFAULT null;
                DECLARE int_campaign_index INT DEFAULT null;
                DECLARE str_campaign_table_name LONGTEXT DEFAULT '';
                DECLARE str_campaign_recipient_table_name LONGTEXT DEFAULT '';

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

                IF (user_id is not null) THEN
            		SET str_campaign_table_name = CONCAT('campaign_u_', user_id);
                    SET str_campaign_recipient_table_name = CONCAT('campaign_recipients_u_', user_id);
            	END IF;

                IF (campaign_id is not null) THEN
            		SET int_campaign_index = campaign_id;
                END IF;

            	START TRANSACTION;
            		SET @query = CONCAT('DELETE FROM ', str_campaign_table_name, ', ', str_campaign_recipient_table_name,
            							' using ', str_campaign_table_name, ' inner join ', str_campaign_recipient_table_name,
                                        ' where ', str_campaign_table_name, '.id = ', str_campaign_recipient_table_name, '.campaign_id and ',
                                        str_campaign_table_name, '.id = ', int_campaign_index,';');

            		PREPARE qr FROM @query;
            		EXECUTE qr;
            		DEALLOCATE PREPARE qr;

            	COMMIT;
            END" );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
        Schema::dropIfExists ( 'campaign_u_template' );

        //
        $sql = "DROP PROCEDURE IF EXISTS load_campaign_data_by_query";
        DB::connection ()->getPdo ()->exec ( $sql );
    }
}
