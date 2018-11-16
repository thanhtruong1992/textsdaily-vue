<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQueueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        // create queue template
        Schema::create ( 'queue_u_template_c_template', function (Blueprint $table) {
            $table->increments ( 'id' );
            $table->string('queue_id', 50)->unique();
            $table->unsignedInteger ( 'list_id' );
            $table->unsignedInteger ( 'subscriber_id' );
            $table->string ( 'phone', 20 );
            $table->string ( 'country', 5 )->nullable();
            $table->string ( 'network', 100 )->nullable();
            $table->tinyInteger('ported')->default(0);
            $table->string( 'service_provider', 25 )->nullable ();
            $table->text('message')->nullable ();
            $table->integer('message_count')->default(0);
            $table->double('sum_price_agency')->default(0);
            $table->double('sum_price_client')->default(0);
            $table->enum ( 'status', ['PENDING', 'SENDING', 'SENT', 'FAILED'] )->default ( 'PENDING' );
            $table->string ( 'return_mccmnc', 15 )->nullable ();
            $table->double ( 'return_price' )->nullable ()->default(0);
            $table->string ( 'return_currency', 3 )->nullable ();
            $table->enum ( 'return_status', ['PENDING', 'DELIVERED', 'EXPIRED', 'FAILED'] )->default( 'PENDING' );
            $table->text ( 'return_status_message' )->nullable ();
            $table->integer( 'return_sms_count' )->nullable ();
            $table->string ( 'return_bulk_id', 45 )->nullable ();
            $table->string ( 'return_message_id', 45 )->nullable ();
            $table->timestamp('report_updated_at')->nullable ();
            $table->unsignedInteger ( 'created_by' )->nullable ();
            $table->unsignedInteger ( 'updated_by' )->nullable ();
            $table->timestamps ();
            // Index
            $table->index ( 'phone' );
            $table->index ( 'list_id' );
            $table->index ( 'subscriber_id' );
            $table->index ( ['country', 'network'] );
            $table->index ( 'service_provider' );
            $table->index ( 'status' );
            $table->index ( 'return_mccmnc' );
            $table->index ( 'return_status' );
            $table->index ( 'return_bulk_id' );
            $table->index ( 'return_message_id' );
            $table->index( 'report_updated_at' );
        } );

        // Stored procedures
        DB::unprepared ( "CREATE PROCEDURE generateQueueTableByCampaign( IN user_id INT, IN campaign_id INT, IN list_string_array VARCHAR(1000), IN global_suppression_list_id INT )
            BEGIN

                DECLARE idx	INT DEFAULT 0;
                DECLARE results LONGTEXT DEFAULT '';

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

                        SET @qrDropTable = CONCAT('DROP TABLE IF EXISTS queue_u_', user_id, '_c_', campaign_id, ';');
                        PREPARE qr FROM @qrDropTable;
                        EXECUTE qr;
                        DEALLOCATE PREPARE qr;

                        SET @queryQueue = CONCAT('CREATE TABLE queue_u_', user_id, '_c_', campaign_id, ' LIKE queue_u_template_c_template;');
                        PREPARE qr FROM @queryQueue;
                        EXECUTE qr;
                        DEALLOCATE PREPARE qr;

                    END IF;

                    IF( list_string_array IS NOT NULL) THEN

            			SET @count = (LENGTH(list_string_array) - LENGTH(REPLACE(list_string_array, ',', '')))/LENGTH(',') + 1;

            			loop_insert: LOOP

            				SET idx = idx + 1;

                            SET @list_id = TRIM(REPLACE(SUBSTRING(SUBSTRING_INDEX(list_string_array, ',', idx), LENGTH(SUBSTRING_INDEX(list_string_array, ',', idx-1)) + 1), ',', ''));

                            IF( CHAR_LENGTH(@list_id) > 0 ) THEN

                				SET @qrInsertData = CONCAT(\"INSERT INTO queue_u_\", user_id, \"_c_\", campaign_id, \" (`list_id`, `subscriber_id`, `phone`, `country`, `network`, `ported`, `service_provider`, `status`, `created_by`, `created_at`) SELECT \", @list_id, \", T1.`id`, T1.`phone`, T1.`country`, T1.`network`, T1.`ported`, T1.`service_provider`, 'PENDING', \", user_id, \", '\", NOW(), \"' FROM subscribers_l_\", @list_id, \" AS T1 LEFT JOIN subscribers_l_\", global_suppression_list_id, \" AS T2 ON T1.phone = T2.phone WHERE T1.`status` = 'SUBSCRIBED' AND T2.id IS NULL;\");
                				PREPARE qr FROM @qrInsertData;
                				EXECUTE qr;
                				DEALLOCATE PREPARE qr;

                            END IF;

                            IF( idx = @count) THEN
            					LEAVE loop_insert;
            				END IF;

                        END LOOP loop_insert;

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
        //
        Schema::dropIfExists ( 'queue_u_template_c_template' );

        // Stored procedures
        DB::unprepared ( "DROP PROCEDURE IF EXISTS generateQueueTableByCampaign" );
    }
}
